<?php

	namespace ApiArchitect\Auth\Providers;

	use Illuminate\Support\ServiceProvider;
	use ApiArchitect\Auth\Http\Parser\Parser;
	use ApiArchitect\Auth\Http\Parser\AuthHeaders;
	/**
	 * Class AuthServiceProvider
	 *
	 * @package ApiArchitect\Auth\Providers
	 * @author James Kirkby <jkirkby91@gmail.com>
	 */
	class AuthServiceProvider extends ServiceProvider
	{
		/**
		 * Register any application services.
		 *
		 * @return void
		 */
		public function register()
		{
			$this->app->bind(
				\ApiArchitect\Auth\Contracts\JWTRequestParserContract::class,
				\ApiArchitect\Auth\Http\Parser\Parser::class
			);

			$this->registerRoutes();
			$this->registerTokenParser();
			$this->registerServiceProviders();
			$this->registerRouteMiddleware();
			$this->registerControllers();
		}

		/**
		 * Boot the authentication services for the application.
		 *
		 * @return void
		 */
		public function boot()
		{
			$this->app->bind(
				\Tymon\JWTAuth\Contracts\JWTSubject::class,
				\ApiArchitect\Auth\Entities\User::class
			);
		}
		
		/**
		 * Register Routes
		 */
		protected function registerRoutes()
		{
			include __DIR__.'/../Http/routes.php';
		}

		/**
		 * Register servcice providers for api architect auth module
		 */
		private function registerServiceProviders()
		{
			$this->app->register(\Tymon\JWTAuth\Providers\LumenServiceProvider::class);
			$this->app->register(\ApiArchitect\Auth\Providers\DoctrineUserAdapterServiceProvider::class);
			$this->app->register(\Jkirkby91\LumenDoctrineComponent\Providers\LumenDoctrineServiceProvider::class);
			$this->app->register(\ApiArchitect\Auth\Providers\UserRepositoryServiceProvider::class);
			$this->app->register(\ApiArchitect\Auth\Providers\PasswordResetsRepositoryServiceProvider::class);
			$this->app->register(\ApiArchitect\Auth\Providers\SocialAccountRepositoryServiceProvider::class);
			$this->app->register(\ApiArchitect\Auth\Providers\AccountServiceProvider::class);

			$this->app->register(\ApiArchitect\FileSystem\Providers\FileSystemServiceProvider::class);

			if(getenv('SOCIALITE_ENABLED') === 'TRUE') {
				$this->app->register(\ApiArchitect\Auth\Providers\SocialiteServiceProvider::class);
			}

			if(getenv('APP_ENV') === 'local') {
				$this->app->register(\ApiArchitect\Auth\Providers\CreateRoleCommandServiceProvider::class);
			}
		}

		/**
		 * Register the bindings for the Token Parser.
		 *
		 * @return void
		 */
		protected function registerTokenParser()
		{
			$this->app->bind(\ApiArchitect\Auth\Http\Parser\Parser::class, function($app) {
				return new \ApiArchitect\Auth\Http\Parser\Parser(
					$app['psr7request'],[new \ApiArchitect\Auth\Http\Parser\AuthHeaders]
				);
			});
		}

		/**
		 * Register app Auth Middleware
		 */
		protected function registerRouteMiddleware()
		{
			$this->app->bind(\ApiArchitect\Auth\Http\Middleware\AuthenticateMiddleware::class, function($app) {
				return new \ApiArchitect\Auth\Http\Middleware\AuthenticateMiddleware(
					new \ApiArchitect\Auth\ApiArchitectAuth(
						$app['tymon.jwt.manager'],
						$app['tymon.jwt.provider.auth'],
						new \ApiArchitect\Auth\Http\Parser\Parser(
							$app['psr7request'],[new \ApiArchitect\Auth\Http\Parser\AuthHeaders]
						)
					)
				);
			});
			
			$this->app->routeMiddleware([
				'psr7adapter' => \Jkirkby91\IlluminateRequestPSR7Adapter\Middleware\PSR7AdapterMiddleware::class,

				'apiarchitect.auth' => \ApiArchitect\Auth\Http\Middleware\AuthenticateMiddleware::class,
				'apiarchitect.refresh' => \Tymon\JWTAuth\Http\Middleware\RefreshToken::class,
				// 'role' => \ApiArchitect\Auth\Http\Controllers\Middleware\RoleMiddleware::class,
			]);
		}

		/**
		 * Register Controllers + inject their transformer
		 */
		public function registerControllers()
		{
			
			$this->app->bind(\ApiArchitect\Auth\Http\Controllers\UserController::class, function($app) {
				return new \ApiArchitect\Auth\Http\Controllers\UserController(
					$app['em']->getRepository(\ApiArchitect\Auth\Entities\User::class),
					new \ApiArchitect\Auth\Http\Transformers\UserTransformer,
					new \ApiArchitect\Auth\ApiArchitectAuth(
						$app['tymon.jwt.manager'],
						$app['tymon.jwt.provider.auth'],
						new \ApiArchitect\Auth\Http\Parser\Parser(
							$app['psr7request'],[new \ApiArchitect\Auth\Http\Parser\AuthHeaders]
						)
					)
				);
			});

			$this->app->bind(\ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController::class, function($app) {
				return new \ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController(
					new \ApiArchitect\Auth\ApiArchitectAuth(
						$app['tymon.jwt.manager'],
						$app['tymon.jwt.provider.auth'],
						new \ApiArchitect\Auth\Http\Parser\Parser(
							$app['psr7request'],[new \ApiArchitect\Auth\Http\Parser\AuthHeaders]
						)
					),
					$app['em']->getRepository(\ApiArchitect\Auth\Entities\User::class),
					new \ApiArchitect\Auth\Http\Transformers\AuthTokenTransformer,
					new \ApiArchitect\Auth\Http\Transformers\UserTransformer
				);
			});
		}

	}
