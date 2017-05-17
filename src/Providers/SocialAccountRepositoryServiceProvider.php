<?php

namespace ApiArchitect\Auth\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ProviderRepositoryServiceProvider
 *
 * @package ApiArchitect\Auth\Providers
 * @author James Kirkby <me@jameskirkby.com>
 */
class SocialAccountRepositoryServiceProvider extends ServiceProvider
{

    /**
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
      $this->app->bind(\ApiArchitect\Auth\Repositories\SocialAccountRepository::class, function($app) {
          // This is what Doctrine's EntityRepository needs in its constructor.
          return new \ApiArchitect\Auth\Repositories\SocialAccountRepository(
              $app['em'],
              $app['em']->getClassMetaData(\ApiArchitect\Auth\Entities\Social\SocialAccount::class)
          );
      });
    }

    /**
     * Get the services provided by the provider since we are deferring load.
     *
     * @return array
     */
    public function provides()
    {
      return ['ApiArchitect\Auth\Repositories\SocialAccountRepository'];
    }
}