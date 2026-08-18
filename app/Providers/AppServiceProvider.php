<?php

namespace App\Providers;

use App\Models\Intervention;
use App\Models\Rapport;
use App\Policies\InterventionPolicy;
use App\Policies\RapportPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
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
        // ── Enregistrement des Policies Laravel ──
        Gate::policy(Intervention::class, InterventionPolicy::class);
        Gate::policy(Rapport::class, RapportPolicy::class);

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
    }
}
