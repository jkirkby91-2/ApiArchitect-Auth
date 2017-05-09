<?php

namespace ApiArchitect\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use ApiArchitect\Auth\Http\Parser\Parser;
use ApiArchitect\Auth\Http\Parser\AuthHeaders;
/**
 * Class AuthServiceProvider
 *
 * @package ApiArchitect\Auth\Providers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerRoutes();
        $this->registerTokenParser();
        $this->registerServiceProviders();
        $this->registerRouteMiddleware();
        $this->registerControllers();

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
    protected function registerRoutes()
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
        $this->app->register(\Laravel\Socialite\SocialiteServiceProvider::class);

        if(getenv('SOCIALITE_ENABLED') === 'TRUE') {        
          $this->app->register(\ApiArchitect\Auth\Providers\SocialiteServiceProvider::class);
        }

        if(getenv('APP_ENV') === 'local') {
            $this->app->register(\ApiArchitect\Auth\Providers\CreateRoleCommandServiceProvider::class);
        }
    }

    /**
     * Register the bindings for the Token Parser.
     *
     * @return void
     */
    protected function registerTokenParser()
    {
       $this->app->bind(\ApiArchitect\Auth\Http\Parser\Parser::class, function($app) {
            return new \ApiArchitect\Auth\Http\Parser\Parser(
                $app['psr7request'],[new AuthHeaders]
            );
        });
    }

    /**
     * Register app Auth Middleware
     */
    protected function registerRouteMiddleware()
    {
        $this->app->routeMiddleware([
            'psr7adapter' => \Jkirkby91\IlluminateRequestPSR7Adapter\Middleware\PSR7AdapterMiddleware::class,
            'apiarchitect.auth' => \ApiArchitect\Auth\Http\Controllers\Authenticate::class,
            'apiarchitect.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
            // 'role' => \ApiArchitect\Auth\Http\Controllers\Middleware\RoleMiddleware::class,
        ]);
    }

     /**
      * Register Controllers + inject their transformer
      */
     public function registerControllers()
     {
         $this->app->bind(\ApiArchitect\Compass\Http\Controllers\User\UserController::class, function($app) {
             return new \ApiArchitect\Compass\Http\Controllers\User\UserController(
                 $app['em']->getRepository(\ApiArchitect\Compass\Entities\User::class),
                 new \ApiArchitect\Compass\Http\Transformers\UserTransformer
             );
         });

         $this->app->bind(\ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController::class, function($app) {
             return new \ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController(
              new \Laravel\Socialite\SocialiteManager($app)
             );
         });         
     }

}
