<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Symfony\Component\Finder\Finder;

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
    // protected $namespace = 'App\\Http\\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->namespace($this->namespace)
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->namespace($this->namespace)
                ->group(base_path('routes/web.php'));

            $this->mapModuleRoutes();
        });
    }

    /**
     * Dynamic module routes
     */
    protected function mapModuleRoutes()
    {
        $finder = new Finder();

        $finder
            ->files()
            ->name('api.php')
            ->name('web.php')
            ->in(base_path('modules'));

        /** @var \Symfony\Component\Finder\SplFileInfo $file */
        foreach ($finder as $file) {
            if ($file->getFilename() === 'api.php') {
                Route::prefix('api')
                    ->middleware('api')
                    ->namespace($this->namespace)
                    ->group($file->getRealPath());
            } else {
                Route::middleware('web')
                    ->namespace($this->namespace)
                    ->group($file->getRealPath());
            }
        }
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
