<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Providers {

		/**
		 * Class PasswordResetsRepositoryServiceProvider
		 *
		 * @package ApiArchitect\Auth\Providers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
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
	}