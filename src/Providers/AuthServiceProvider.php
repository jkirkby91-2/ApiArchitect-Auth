<?php

namespace ApiArchitect\Auth\Providers;

/**
 * Class AuthServiceProvider
 * @package ApiArchitect\Auth\Providers
 */
class AuthServiceProvider extends \Illuminate\Support\ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRoutes();
        $this->registerServiceProviders();
        $this->registerRouteMiddleware();
        $this->registerController();
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register Routes
     */
    public function registerRoutes()
    {
        include __DIR__.'/../Http/routes.php';
    }

    private function registerServiceProviders()
    {
        $this->app->register(\Jkirkby91\LumenDoctrineComponent\Providers\LumenDoctrineServiceProvider::class);
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\DoctrineUserAdapterServiceProvider::class);
    }

    /**
     * Register app Auth Middleware
     */
    public function registerRouteMiddleware()
    {
        $this->app->routeMiddleware([
            'jwt-auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        ]);

    }

    public function registerController()
    {
        $this->app->make('ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController');
    }
}