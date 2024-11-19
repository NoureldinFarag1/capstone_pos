<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route; // Import the Route facade
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Cache\RateLimiter as Limiter; // Import Limiter for clarity
use Illuminate\Support\Facades\RateLimiter as FacadesRateLimiter;

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
    public function boot(): void
    {
        // Optional: Configure rate limiting if needed
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        FacadesRateLimiter::for('api', function (Request $request) {
            return Limiter::perMinute(60); // This is now correct usage
        });
    }
}
