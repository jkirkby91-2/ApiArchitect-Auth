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
        $this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\DoctrineUserAdapterServiceProvider::class);
        $this->app->register(\Jkirkby91\LumenDoctrineComponent\Providers\LumenDoctrineServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\UserRepositoryServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\PasswordResetsRepositoryServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\SocialAccountRepositoryServiceProvider::class);
        $this->app->register(\ApiArchitect\Auth\Providers\AccountServiceProvider::class);

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
            'apiarchitect.auth' => \ApiArchitect\Auth\Http\Middleware\Authenticate::class,
            'apiarchitect.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
            // 'role' => \ApiArchitect\Auth\Http\Controllers\Middleware\RoleMiddleware::class,
        ]);
    }

     /**
      * Register Controllers + inject their transformer
      */
     public function registerControllers()
     {
         $this->app->bind(\ApiArchitect\Auth\Http\Controllers\UserController::class, function($app) {
             return new \ApiArchitect\Auth\Http\Controllers\UserController(
                 $app['em']->getRepository(\ApiArchitect\Auth\Entities\User::class),
                 new \ApiArchitect\Auth\Http\Transformers\UserTransformer
             );
         });

        $this->app->bind(\ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController::class, function($app) {
               return new \ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController(
                  new \Tymon\JWTAuth\JWTAuth(
                    $app['tymon.jwt.manager'],
                    $app['tymon.jwt.provider.auth'],
                    $app['tymon.jwt.parser']
                  ),
                  $app['em']->getRepository(\ApiArchitect\Auth\Entities\User::class),
                  new \ApiArchitect\Auth\Http\Transformers\AuthTokenTransformer,                  
                  new \ApiArchitect\Auth\Http\Transformers\UserTransformer
               );
           });
     }

}
