<?php

namespace ApiArchitect\Auth\Providers;

/**
 * Class AuthServiceProvider
 *
 * @package ApiArchitect\Auth\Providers
 * @author James Kirkby <jkirkby91@gmail.com>
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

        //bind password entity to entity contract implementation
        $this->app->bind(
            '\Jkirkby91\Boilers\NodeEntityBoiler\EntityContract',
            '\ApiArchitect\Auth\Entities\PasswordResets'
        );
    }

    /**
     * Boot the authentication services for the application.
     *
     * @return void
     */
    public function boot(){}

    /**
     * Register Routes
     */
    public function registerRoutes()
    {
        include __DIR__.'/../Http/routes.php';
    }

    /**
     * Register servcice providers for api architect auth module
     */
    private function registerServiceProviders()
    {
        $this->app->register(\Jkirkby91\LumenDoctrineComponent\Providers\LumenDoctrineServiceProvider::class);
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\DoctrineUserAdapterServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\PasswordResetsRepositoryServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\CreateRoleCommandServiceProvider::class);
    }

    /**
     * Register app Auth Middleware
     */
    public function registerRouteMiddleware()
    {
        $this->app->routeMiddleware([
            'jwt-auth' => \Tymon\JWTAuth\Http\Middleware\Authenticate::class,
        ]);

        $this->app->middleware([
            \ApiArchitect\Auth\Http\Middleware\ApiArchitectAuthMiddleware::class
        ]);
    }
}