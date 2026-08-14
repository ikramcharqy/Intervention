<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\Intervention;
use App\Policies\InterventionPolicy;

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
        // Register the Intervention policy so Gate::authorize and $this->authorize work.
        Gate::policy(Intervention::class, InterventionPolicy::class);
    }
}
