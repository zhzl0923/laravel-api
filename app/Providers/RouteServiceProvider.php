<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * The controller namespace for the application.
     *
     * When present, controller route declarations will automatically be prefixed with this namespace.
     *
     * @var string|null
     */
    protected $api = 'App\\Http\\Controllers\\Api';
    protected $admin = 'App\\Http\\Controllers\\Admin';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        $api = app('Dingo\Api\Routing\Router');
        $this->routes(function () use ($api) {
            $api->version('v1', ['prefix' => 'admin', 'namespace' => $this->admin], function ($api) {
                $files = glob(base_path('routes') . '/admin/*.php');
                foreach ($files as $file) {
                    include $file;
                }
            });

            Route::prefix('api')
                ->group(function () use ($api) {
                    $api->version('v1', ['prefix' => 'api', 'namespace' => $this->api], function ($api) {
                        $files = glob(base_path('routes') . '/api/*.php');
                        foreach ($files as $file) {
                            include $file;
                        }
                    });
                });
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by(optional($request->user())->id ?: $request->ip());
        });
    }
}
