<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Devis;
use App\Models\Emplacement;
use App\Models\Intervention;
use App\Models\Prospect;
use App\Models\Rapport;
use App\Observers\ClientObserver;
use App\Observers\DevisObserver;
use App\Observers\ProspectObserver;
use App\Policies\ClientPolicy;
use App\Policies\EmplacementPolicy;
use App\Policies\InterventionPolicy;
use App\Policies\RapportPolicy;
use App\Models\AuditLog;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

        if (config('app.env') !== 'local') {
          \Illuminate\Support\Facades\URL::forceScheme('https');
        }

        // ── Enregistrement des Policies Laravel ──
        Gate::policy(Intervention::class, InterventionPolicy::class);
        Gate::policy(Rapport::class, RapportPolicy::class);
        Gate::policy(Client::class, ClientPolicy::class);
        Gate::policy(Emplacement::class, EmplacementPolicy::class);

        // ── Observers ──
        Client::observe(ClientObserver::class);
        Prospect::observe(ProspectObserver::class);
        Devis::observe(DevisObserver::class);

        // ── Rate Limiting centralisé ──
        // Login : 6 tentatives / 1 minute par IP
        RateLimiter::for('api-login', function (Request $request) {
            return Limit::perMinute(6)->by($request->ip());
        });

        // API générale : 120 requêtes / minute par user ou IP
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by($request->user()->id)
                : Limit::perMinute(30)->by($request->ip());
        });

        // Endpoints sensibles (rapport, formulaire) : 30 / minute
        RateLimiter::for('api-upload', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(30)->by($request->user()->id)
                : Limit::perMinute(10)->by($request->ip());
        });

        // Trace de dernière connexion (affichée dans Portail Client > Sécurité).
        Event::listen(function (Login $event) {
            $event->user->forceFill([
                'last_login_at' => now(),
                'last_login_ip' => request()->ip(),
            ])->saveQuietly();

            // Historique de connexion (Portail Client > Sécurité) — additif, même
            // événement, ne modifie rien pour les autres rôles au-delà d'une ligne
            // de log supplémentaire par connexion.
            \App\Models\LoginHistory::create([
                'user_id' => $event->user->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'logged_in_at' => now(),
            ]);
        });

        // Étape 5 du prompt "Vérification du contournement 2FA" : tentative de connexion
        // échouée (mauvais mot de passe) — l'échec de code 2FA est déjà journalisé
        // séparément dans TwoFactorAuthService::verify(). $event->user est null si
        // l'email ne correspond à aucun compte (à tracer quand même : utile pour repérer
        // un balayage d'emails).
        Event::listen(function (Failed $event) {
            $emailTente = $event->credentials['email'] ?? 'inconnu';

            AuditLog::create([
                'user_id' => $event->user?->id,
                'user_name' => $event->user?->name ?? $emailTente,
                'action' => "Échec de connexion (mot de passe incorrect) pour {$emailTente}",
                'module' => 'Security',
                'category' => AuditLog::CATEGORY_SECURITY,
                'severity' => 'WARNING',
                'ip_address' => request()->ip(),
            ]);
        });

        // Étape 1 : un compte administrateur invité (mot de passe aléatoire, jamais
        // transmis) termine son inscription via le lien "réinitialiser le mot de passe"
        // envoyé à la création — on marque ici l'invitation comme acceptée.
        Event::listen(function (PasswordReset $event) {
            if ($event->user->invitation_accepted_at === null) {
                $event->user->forceFill(['invitation_accepted_at' => now()])->saveQuietly();

                AuditLog::create([
                    'user_id' => $event->user->id,
                    'user_name' => $event->user->name,
                    'action' => "Invitation acceptée par {$event->user->email} (mot de passe défini)",
                    'module' => 'Security',
                    'category' => AuditLog::CATEGORY_SECURITY,
                    'severity' => 'SUCCESS',
                    'ip_address' => request()->ip(),
                ]);
            }
        });
    }
}
