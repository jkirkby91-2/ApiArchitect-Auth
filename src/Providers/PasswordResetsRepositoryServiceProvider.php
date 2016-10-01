<?php

namespace ApiArchitect\Auth\Providers;

/**
 * Class AppServiceProvider
 *
 * @package app\Providers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class PasswordResetsRepositoryServiceProvider extends \Illuminate\Support\ServiceProvider
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
        $this->app->bind(\ApiArchitect\Auth\Repositories\PasswordResetsRepository::class, function($app) {
            // This is what Doctrine's EntityRepository needs in its constructor.
            return new \ApiArchitect\Auth\Repositories\PasswordResetsRepository(
                $app['em'],
                $app['em']->getClassMetaData(\ApiArchitect\Auth\Entities\PasswordResets::class)
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
        return ['\ApiArchitect\Auth\Repositories\PasswordResetsRepository'];
    }
}