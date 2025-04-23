<?php

namespace App\Providers;

use App\Models\MenuItem;
use App\Policies\MenuPolicy;
use Illuminate\Support\Facades\Gate;
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
        Gate::policy(MenuItem::class, MenuPolicy::class);
    }
}
