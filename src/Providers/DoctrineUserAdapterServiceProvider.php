<?php

namespace ApiArchitect\Auth\Providers;

/**
 * Class DoctrineUserAdapterServiceProvider
 *
 * @package app\Providers
 * @author James Kirkby <hello@jameskirkby.com>
 */
class DoctrineUserAdapterServiceProvider extends \Illuminate\Support\ServiceProvider
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
        $this->app->bind(\ApiArchitect\Auth\Adapters\DoctrineUserAdapter::class, function($app) {
            return new \ApiArchitect\Auth\Adapters\DoctrineUserAdapter(
                new \LaravelDoctrine\ORM\Auth\DoctrineUserProvider(
                    $app['hash'],
                    $app['em'],
                    \ApiArchitect\Compass\Entities\User::class
                )
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
        return ['ApiArchitect\Auth\Adapters\DoctrineUserAdapter'];
    }
}