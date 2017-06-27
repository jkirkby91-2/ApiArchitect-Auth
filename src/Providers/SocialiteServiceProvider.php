<?php

namespace ApiArchitect\Auth\Providers;

use Illuminate\Support\ServiceProvider;
use ApiArchitect\Auth\Http\Parser\Parser;
use ApiArchitect\Auth\Http\Parser\AuthHeaders;

/**
 * Class SocialiteServiceProvider
 *
 * @package ApiArchitect\Auth\Providers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class SocialiteServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      $this->registerServiceProviders();
//      $this->registerControllers();
		$this->app->bind(
			'\ApiArchitect\Auth\Contracts\JWTRequestParserContract',
			'\ApiArchitect\Auth\Http\Parser\Parser'
		);

		$this->registerControllers();
    }

    public function boot()
	{

	}

    /**
     * Register Service providers for Socialite
     */
    private function registerServiceProviders()
    {
      $this->app->register(\Laravel\Socialite\SocialiteServiceProvider::class);
      $this->app->register(\ApiArchitect\Auth\Providers\ProviderRepositoryServiceProvider::class);

      if(getenv('APP_ENV') === 'local') {
        $this->app->register(\ApiArchitect\Auth\Providers\CreateProviderCommandServiceProvider::class);
      }
    }

     /**
      * Register Controllers + inject their transformer
      */
     public function registerControllers()
     {

      $this->app->bind(\ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController::class, function($app) {
             return new \ApiArchitect\Auth\Http\Controllers\Auth\Socialite\OauthController(
              new \Laravel\Socialite\SocialiteManager($app),
              $app['em']->getRepository(\ApiArchitect\Auth\Entities\User::class),
              new \ApiArchitect\Auth\ApiArchitectAuth(
                $app['tymon.jwt.manager'],
                $app['tymon.jwt.provider.auth'],
				  new \ApiArchitect\Auth\Http\Parser\Parser(
					  $app['psr7request'],[new \ApiArchitect\Auth\Http\Parser\AuthHeaders]
				  )
              ),
              new \ApiArchitect\Auth\Http\Transformers\AuthTokenTransformer
             );
         });
     }
}
