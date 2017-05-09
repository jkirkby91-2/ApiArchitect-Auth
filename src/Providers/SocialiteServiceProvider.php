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
}
