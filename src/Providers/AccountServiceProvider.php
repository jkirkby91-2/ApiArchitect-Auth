<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Providers {

		use Illuminate\{
			Support\ServiceProvider
		};

		use ApiArchitect\{
			Auth\Services\AccountService
		};

		/**
		 * Class AccountServiceProvider
		 *
		 * @package ApiArchitect\Auth\Providers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class AccountServiceProvider extends ServiceProvider
		{

			/**
			 * Register any application services.
			 *
			 * @return void
			 */
			public function boot()
			{
				$this->app['apiarchitect.account.service'] = new AccountService(
					$this->app['em']->getRepository(\ApiArchitect\Auth\Entities\User::class
					)
				);
			}
		}
	}
