<?php

namespace App\Providers;

use App\Http\Middleware\CheckCashDrawer;
use Illuminate\Support\Facades\Route; // Import the Route facade
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\RateLimiter; // Use only this for RateLimiter
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Gate;
use App\Http\Middleware\RoleMiddleware;
use App\Http\Middleware\RedirectBasedOnRole;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers'; // Define the namespace for your controllers

    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        // Fix: Correct way to register middleware alias
        $this->app['router']->aliasMiddleware('role.redirect', RedirectBasedOnRole::class);

        // Define gates directly without registerPolicies
        Gate::define('admin', function ($user) {
            return $user->hasRole('admin');
        });

        Gate::define('moderator', function ($user) {
            return $user->hasRole('moderator');
        });

        $this->configureRateLimiting();
        app('router')->aliasMiddleware('role', RoleMiddleware::class);
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return \Illuminate\Cache\RateLimiting\Limit::perMinute(60); // Correct usage
        });
    }

    protected function mapWebRoutes()
    {
        Route::middleware(['web', CheckCashDrawer::class])
             ->group(base_path('routes/web.php'));
    }

}
