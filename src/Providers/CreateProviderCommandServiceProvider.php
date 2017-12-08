<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Providers {

		use Illuminate\{
			Support\ServiceProvider
		};

		use ApiArchitect\{
			Auth\Console\Commands\CreateProviderCommand
		};

		/**
		 * Class CreateProviderCommandServiceProvider
		 *
		 * @package ApiArchitect\Auth\Providers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class CreateProviderCommandServiceProvider extends ServiceProvider
		{

			/**
			 * Register any application services.
			 *
			 * @return void
			 */
			public function register()
			{
				$this->app->singleton('command.create:provider', function()
				{
					return new CreateProviderCommand();
				});

				$this->commands(
					'command.create:provider'
				);
			}
		}
	}
