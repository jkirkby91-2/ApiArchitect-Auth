<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Providers {

		/**
		 * Class DoctrineUserAdapterServiceProvider
		 *
		 * @package ApiArchitect\Auth\Providers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
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
							\ApiArchitect\Auth\Entities\User::class
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
	}
