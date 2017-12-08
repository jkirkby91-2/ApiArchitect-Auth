<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Middleware {

		use Closure;

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use ApiArchitect\{
			Compass\Http\Middleware\AbstractMiddleware
		};

		class RoleMiddleware extends AbstractMiddleware
		{

			/**
			 * handle()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 * @param \Closure                                 $next
			 * @param                                          $role
			 *
			 * @return \Illuminate\Http\RedirectResponse|\Laravel\Lumen\Http\Redirector|mixed
			 * @TODO
			 */
			public function handle(ServerRequestInterface $request, Closure $next, $role) : Closure
			{
				$user = $this->auth->parseToken()->authenticate();
				if (!$user->hasRole($role)) {
					return redirect('/login');
				}

				return $next($request);
			}

		}
	}