<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Middleware {

		use ApiArchitect\{
			Auth\ApiArchitectAuth, Auth\Entities\User, Auth\Http\Parser\Parser, Compass\Http\Middleware\AbstractMiddleware
		};

		use Closure;

		use Tymon\{
			JWTAuth\Exceptions\JWTException
		};

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use Symfony\{
			Component\HttpKernel\Exception\BadRequestHttpException,
			Component\HttpKernel\Exception\UnauthorizedHttpException
		};

		/**
		 * Class AuthenticateMiddleware
		 *
		 * @package ApiArchitect\Auth\Http\Middleware
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class AuthenticateMiddleware extends AbstractMiddleware
		{

			/**
			 * @var \ApiArchitect\Auth\ApiArchitectAuth|\ApiArchitect\Auth\Http\Middleware\Guard $auth
			 */
			protected $auth;

			/**
			 * @var \ApiArchitect\Auth\Entities\User $user
			 */
			protected $user;

			/**
			 * AuthenticateMiddleware constructor.
			 *
			 * @param \ApiArchitect\Auth\ApiArchitectAuth $auth
			 */
			public function __construct(ApiArchitectAuth $auth)
			{
				$this->auth = $auth;
			}

			/**
			 * handle()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 * @param \Closure                                 $next
			 *
			 * @return \Closure
			 */
			public function handle(ServerRequestInterface $request, Closure $next) : Closure
			{
				$this->user = $this->authenticate($request);

				return $next($request);
			}

			/**
			 * checkForToken()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return bool
			 */
			public function checkForToken(ServerRequestInterface $request) : bool
			{
				if (! $this->auth->getParser()->setRequest($request)->hasToken()) {
					throw new BadRequestHttpException('Token not provided');
				}

				return true;
			}

			/**
			 * authenticate()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function authenticate(ServerRequestInterface $request) : User
			{
				$this->checkForToken($request);

				$payload = $this->auth->getManager()
					->getJWTProvider()->decode($this->auth->getParser()->parseToken());

				try {
					return $this->auth->getProvider()->byId($payload['sub']);
				} catch (JWTException $e) {
					throw new UnauthorizedHttpException('ApiArchitect.Auth', $e->getMessage(), $e, $e->getCode());
				}
			}
		}
	}
