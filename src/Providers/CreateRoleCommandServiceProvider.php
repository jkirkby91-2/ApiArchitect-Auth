<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Providers {

		use Illuminate\{
			Support\ServiceProvider
		};

		use ApiArchitect\{
			Auth\Console\Commands\CreateRoleCommand
		};

		/**
		 * Class CreateRoleCommandServiceProvider
		 *
		 * @package ApiArchitect\Auth\Providers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class CreateRoleCommandServiceProvider extends ServiceProvider
		{

			/**
			 * Register any application services.
			 *
			 * @return void
			 */
			public function register()
			{
				$this->app->singleton('command.create:role', function()
				{
					return new CreateRoleCommand();
				});

				$this->commands(
					'command.create:role'
				);
			}
		}
	}
