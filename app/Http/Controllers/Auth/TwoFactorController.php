<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\TwoFactorAuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Contrôleur fin : toute la logique d'enrôlement/vérification vit dans
 * TwoFactorAuthService, conformément à l'architecture Controller → Service → Model.
 */
class TwoFactorController extends Controller
{
    public function __construct(private TwoFactorAuthService $twoFactor)
    {
    }

    public function setup(): View|RedirectResponse
    {
        $user = auth()->user();

        if ($user->two_factor_confirmed_at) {
            return redirect()->route('superadmin.dashboard')->with('success', 'La 2FA est déjà activée sur votre compte.');
        }

        // Idempotent : réutilise le secret déjà en attente de confirmation s'il existe
        // (un rechargement de cette page ne doit jamais invalider le QR code déjà scanné
        // par l'utilisateur) — un nouveau secret n'est généré qu'à la toute première
        // visite. Un enrôlement abandonné se corrige via /2fa/reset (step-up auth), pas
        // en régénérant silencieusement à chaque affichage.
        $this->twoFactor->ensureSecret($user);
        $user->refresh();

        return view('auth.two-factor-setup', [
            'qrCodeSvg' => $this->twoFactor->getQrCodeSvg($user),
        ]);
    }

    public function confirm(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $recoveryCodes = $this->twoFactor->confirm(auth()->user(), $request->input('code'));

        if ($recoveryCodes === false) {
            return back()->withErrors(['code' => 'Code incorrect. Vérifiez votre application d\'authentification.']);
        }

        $request->session()->put('2fa_passed_at', time());
        $request->session()->flash('recovery_codes', $recoveryCodes);

        return redirect()->route('two-factor.recoveryCodes');
    }

    /**
     * Affichage unique des codes de secours — uniquement accessible juste après confirm()
     * via la donnée flashée en session ; un rechargement direct de l'URL sans ce flash
     * ne réaffiche jamais les codes (ils ne sont plus disponibles en clair ensuite).
     */
    public function recoveryCodes(Request $request): View|RedirectResponse
    {
        $codes = $request->session()->get('recovery_codes');

        if (!$codes) {
            return redirect()->route('superadmin.dashboard');
        }

        return view('auth.two-factor-recovery-codes', ['codes' => $codes]);
    }

    public function challenge(): View
    {
        return view('auth.two-factor-challenge');
    }

    public function verify(Request $request): RedirectResponse
    {
        $request->validate(['code' => 'required|string']);

        $user = auth()->user();

        // Étape 4 : verrouillage anti-brute-force — vérifié avant toute tentative, quel
        // que soit le code fourni.
        if ($this->twoFactor->isLockedOut($user)) {
            $minutes = (int) ceil($this->twoFactor->lockoutSecondsRemaining($user) / 60);
            return back()->withErrors(['code' => "Trop de tentatives échouées. Réessayez dans {$minutes} minute(s)."]);
        }

        $code = $request->input('code');

        $valid = $this->twoFactor->verify($user, $code)
            || $this->twoFactor->useRecoveryCode($user, $code);

        if (!$valid) {
            // Message volontairement générique — ne révèle jamais si l'échec vient d'un
            // TOTP invalide ou d'un code de secours invalide (Étape 4.3).
            $this->twoFactor->registerFailedTwoFactorAttempt($user);
            return back()->withErrors(['code' => 'Code incorrect.']);
        }

        $this->twoFactor->clearFailedTwoFactorAttempts($user);

        $request->session()->put('2fa_passed_at', time());

        // Pas de redirect()->intended() : le fallback était câblé en dur vers
        // superadmin.dashboard, envoyant aussi les comptes 'admin' (soumis à la même
        // 2FA obligatoire) vers une page qu'ils n'ont pas le droit de voir (403).
        $request->session()->forget('url.intended');

        return redirect()->route($user->dashboardRouteName());
    }

    /**
     * Réinitialisation de la 2FA — la route est groupée sous le middleware
     * 'password.confirm' (step-up auth), la ré-authentification par mot de passe est
     * donc déjà garantie avant d'atteindre cette action.
     */
    public function reset(Request $request): RedirectResponse
    {
        $this->twoFactor->reset(auth()->user());
        $request->session()->forget('2fa_passed_at');

        return redirect()->route('two-factor.setup')->with('success', 'La 2FA a été réinitialisée. Un nouvel enrôlement est requis.');
    }
}
