<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use PragmaRX\Google2FAQRCode\Google2FA;

/**
 * Flux d'enrôlement 2FA (TOTP) complet : génération de secret, QR code SVG local
 * (aucun appel à un service externe type Google Charts), confirmation avec émission
 * de codes de secours à usage unique, vérification à la connexion, et réinitialisation.
 *
 * Le secret et les codes de secours sont stockés chiffrés (`encrypted` cast sur le
 * modèle User) — jamais en clair après confirmation. Les codes de secours ne sont
 * renvoyés en clair qu'une seule fois, au moment de `confirm()`.
 */
class TwoFactorAuthService
{
    /**
     * Rôles pour lesquels la 2FA est obligatoire (cf. EnsureTwoFactorForSuperAdmin) —
     * dupliqué volontairement ici plutôt que partagé avec le middleware : liste stable,
     * et coupler les deux forcerait un couplage Controller/API → Middleware web hors de
     * la portée de ce correctif.
     */
    public const ROLES_REQUIRING_2FA = ['Super Admin', 'admin', 'Administrateur'];

    /**
     * Étape 4 du prompt "Investigation des comptes manquants" : verrouillage anti-
     * brute-force sur la vérification du code TOTP/codes de secours au challenge de
     * connexion — 5 échecs consécutifs déclenchent un blocage de 15 minutes, journalisé
     * en sévérité CRITICAL (au lieu de WARNING pour un simple échec isolé).
     */
    private const MAX_TENTATIVES_2FA = 5;
    private const VERROUILLAGE_SECONDES = 900; // 15 minutes

    private Google2FA $google2fa;

    public function __construct()
    {
        $this->google2fa = new Google2FA();
    }

    /**
     * Étape 2 du prompt "Vérification du contournement 2FA" : un compte à privilège
     * élevé ne doit jamais pouvoir s'authentifier par un canal qui ne fait pas respecter
     * la 2FA obligatoire (ex: /api/login, prévu pour l'app mobile Technicien) — la 2FA
     * n'y est pas vérifiable (pas de session web, pas de challenge TOTP dans ce flux).
     */
    public function accountRequiresMandatory2FA(User $user): bool
    {
        return $user->hasAnyRole(self::ROLES_REQUIRING_2FA);
    }

    /**
     * Génère un nouveau secret TOTP non confirmé pour l'enrôlement. Écrase tout secret
     * précédent non confirmé (recommencer l'enrôlement est sans risque tant qu'il n'est
     * pas confirmé) mais refuse d'écraser un secret déjà confirmé — passer par reset().
     */
    public function generateSecret(User $user): string
    {
        if ($user->two_factor_confirmed_at) {
            throw new \RuntimeException('La 2FA est déjà activée sur ce compte — réinitialisez-la avant d\'en générer une nouvelle.');
        }

        $secret = $this->google2fa->generateSecretKey();

        $user->forceFill(['two_factor_secret' => $secret])->save();

        return $secret;
    }

    /**
     * Idempotent : réutilise le secret non confirmé déjà en attente s'il en existe un
     * (cas normal — un simple rechargement de /2fa/setup ne doit jamais invalider le QR
     * code déjà scanné), et n'appelle generateSecret() que lors de la toute première
     * visite (aucun secret existant). Un secret déjà confirmé fait toujours échouer
     * l'appel — cf. generateSecret() — repasser par reset() pour en générer un nouveau.
     */
    public function ensureSecret(User $user): string
    {
        if ($user->two_factor_confirmed_at) {
            throw new \RuntimeException('La 2FA est déjà activée sur ce compte — réinitialisez-la avant d\'en générer une nouvelle.');
        }

        if ($user->two_factor_secret) {
            return $user->two_factor_secret;
        }

        return $this->generateSecret($user);
    }

    /**
     * Rendu QR code en SVG, généré localement (Bacon\QrCode, backend Svg par défaut en
     * l'absence d'ext-imagick) — jamais transmis à un service tiers.
     */
    public function getQrCodeSvg(User $user): string
    {
        if (!$user->two_factor_secret) {
            throw new \RuntimeException('Aucun secret TOTP en attente de confirmation pour ce compte.');
        }

        $svg = $this->google2fa->getQRCodeInline(
            config('app.name', 'TechniTrack'),
            $user->email,
            $user->two_factor_secret
        );

        // getQRCodeInline() renvoie le SVG précédé de son prologue XML (balise de
        // déclaration XML en tête). Une fois inséré tel quel dans du HTML via {!! !!}
        // (au lieu d'un document XML autonome), ce prologue est invalide et rend
        // l'affichage du QR code peu fiable selon le navigateur — on ne garde que le
        // <svg> proprement dit.
        return preg_replace('/^\s*<\?xml[^>]*\?>\s*/i', '', $svg);
    }

    /**
     * Vérifie le code de confirmation d'enrôlement, active la 2FA et génère les codes de
     * secours (retournés en clair une seule fois — à afficher immédiatement à l'appelant,
     * jamais journalisés ni réaffichés ensuite).
     *
     * @return array<string>|false Les codes de secours en clair, ou false si le code est invalide.
     */
    public function confirm(User $user, string $code): array|false
    {
        if (!$user->two_factor_secret || !$this->google2fa->verifyKey($user->two_factor_secret, $code)) {
            return false;
        }

        $recoveryCodes = $this->generateRecoveryCodes();

        $user->forceFill([
            'two_factor_confirmed_at' => now(),
            'two_factor_recovery_codes' => $recoveryCodes,
        ])->save();

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => "Activation de la 2FA pour {$user->email}",
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'SUCCESS',
            'ip_address' => request()->ip(),
        ]);

        return $recoveryCodes;
    }

    /**
     * Étape 4 : à appeler AVANT toute tentative de vérification — bloque l'accès au
     * challenge tant que le verrouillage n'est pas expiré, indépendamment du fait que le
     * prochain code fourni serait correct ou non (empêche un attaquant de continuer à
     * essayer pendant la fenêtre de blocage).
     */
    public function isLockedOut(User $user): bool
    {
        return RateLimiter::tooManyAttempts($this->lockoutKey($user), self::MAX_TENTATIVES_2FA);
    }

    public function lockoutSecondsRemaining(User $user): int
    {
        return RateLimiter::availableIn($this->lockoutKey($user));
    }

    /**
     * Enregistre un échec (TOTP invalide OU code de secours invalide — le compteur est
     * commun aux deux, cohérent avec le message générique qui ne distingue jamais lequel
     * a échoué). Journalise en sévérité CRITICAL le passage au-dessus du seuil (pas à
     * chaque échec — seulement celui qui déclenche effectivement le blocage).
     */
    public function registerFailedTwoFactorAttempt(User $user): void
    {
        $key = $this->lockoutKey($user);
        $etaitDejaVerrouille = RateLimiter::tooManyAttempts($key, self::MAX_TENTATIVES_2FA);

        RateLimiter::hit($key, self::VERROUILLAGE_SECONDES);

        if (!$etaitDejaVerrouille && RateLimiter::tooManyAttempts($key, self::MAX_TENTATIVES_2FA)) {
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => "Verrouillage temporaire (15 minutes) après " . self::MAX_TENTATIVES_2FA . " échecs consécutifs de vérification 2FA pour {$user->email}",
                'module' => 'Security',
                'category' => AuditLog::CATEGORY_SECURITY,
                'severity' => 'CRITICAL',
                'ip_address' => request()->ip(),
            ]);
        }
    }

    public function clearFailedTwoFactorAttempts(User $user): void
    {
        RateLimiter::clear($this->lockoutKey($user));
    }

    private function lockoutKey(User $user): string
    {
        return 'two-factor-verify:' . $user->id;
    }

    /**
     * Vérifie un code TOTP à la connexion (secret déjà confirmé).
     */
    public function verify(User $user, string $code): bool
    {
        if (!$user->two_factor_secret) {
            return false;
        }

        $valid = $this->google2fa->verifyKey($user->two_factor_secret, $code);

        if (!$valid) {
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => "Échec de vérification 2FA pour {$user->email}",
                'module' => 'Security',
                'category' => AuditLog::CATEGORY_SECURITY,
                'severity' => 'WARNING',
                'ip_address' => request()->ip(),
            ]);
        }

        return $valid;
    }

    /**
     * Consomme un code de secours à usage unique (perte d'accès à l'application TOTP).
     */
    public function useRecoveryCode(User $user, string $code): bool
    {
        $codes = $user->two_factor_recovery_codes ?? [];
        $normalized = strtoupper(trim($code));
        $index = array_search($normalized, $codes, true);

        if ($index === false) {
            AuditLog::create([
                'user_id' => $user->id,
                'user_name' => $user->name,
                'action' => "Tentative de code de secours 2FA invalide pour {$user->email}",
                'module' => 'Security',
                'category' => AuditLog::CATEGORY_SECURITY,
                'severity' => 'WARNING',
                'ip_address' => request()->ip(),
            ]);
            return false;
        }

        unset($codes[$index]);
        $user->forceFill(['two_factor_recovery_codes' => array_values($codes)])->save();

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => "Code de secours 2FA utilisé pour {$user->email} (" . count($codes) . ' restant(s))',
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => request()->ip(),
        ]);

        return true;
    }

    /**
     * Réinitialisation complète (perte d'accès définitive, ou secret compromis) —
     * l'appelant est responsable d'exiger une ré-authentification par mot de passe
     * avant d'invoquer cette méthode (cf. middleware 'password.confirm' sur la route).
     */
    public function reset(User $user): void
    {
        $user->forceFill([
            'two_factor_secret' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_recovery_codes' => null,
        ])->save();

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => "Réinitialisation de la 2FA pour {$user->email}",
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'WARNING',
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * Étape 5 : rappel manuel envoyé à un administrateur dont l'invitation est acceptée
     * mais qui n'a toujours pas confirmé sa 2FA obligatoire. Action non destructive et
     * réversible (un simple email) — pas de step-up auth requis, contrairement à
     * reset() ci-dessus.
     */
    public function sendActivationReminder(User $user): void
    {
        $user->notify(new \App\Notifications\RappelActivation2FANotification());

        AuditLog::create([
            'user_id' => $user->id,
            'user_name' => $user->name,
            'action' => "Rappel d'activation 2FA envoyé à {$user->email}",
            'module' => 'Security',
            'category' => AuditLog::CATEGORY_SECURITY,
            'severity' => 'INFO',
            'ip_address' => request()->ip(),
        ]);
    }

    /**
     * @return array<string>
     */
    private function generateRecoveryCodes(int $count = 10): array
    {
        return collect(range(1, $count))
            ->map(fn () => strtoupper(Str::random(4) . '-' . Str::random(4)))
            ->all();
    }
}
