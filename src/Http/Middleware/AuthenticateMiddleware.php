<?php

	namespace ApiArchitect\Auth\Http\Middleware;

	use ApiArchitect\Auth\ApiArchitectAuth;
	use Closure;
	use Tymon\JWTAuth\JWTAuth;
	use ApiArchitect\Auth\Http\Parser\Parser;
	use Tymon\JWTAuth\Exceptions\JWTException;
	use Psr\Http\Message\ServerRequestInterface;
	use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
	use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
	use ApiArchitect\Compass\Http\Middleware\AbstractMiddleware;

	/**
	 * Class AuthenticateMiddleware
	 * @package ApiArchitect\Auth\Http\Middleware
	 */
	class AuthenticateMiddleware extends AbstractMiddleware
	{

		/**
		 * @var \ApiArchitect\Auth\ApiArchitectAuth|\ApiArchitect\Auth\Http\Middleware\Guard
		 */
		protected $auth;

		/**
		 * @var
		 */
		protected $user;

		/**
		 * Authenticate constructor.
		 * @param Guard $auth
		 */
		public function __construct(ApiArchitectAuth $auth)
		{
			$this->auth = $auth;
		}

		/**
		 * Handle an incoming request.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 * @param  \Closure  $next
		 * @return mixed
		 */
		public function handle(ServerRequestInterface $request, Closure $next)
		{
			$this->user = $this->authenticate($request);

			return $next($request);
		}

		/**
		 * Check the request for the presence of a token.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 *
		 * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
		 *
		 * @return void
		 */
		public function checkForToken(ServerRequestInterface $request)
		{
			if (! $this->auth->getParser()->setRequest($request)->hasToken()) {
				throw new BadRequestHttpException('Token not provided');
			}
		}

		/**
		 * authenticate()
		 *
		 * Attempt to authenticate a user via the token in the request.
		 *
		 * @param \Psr\Http\Message\ServerRequestInterface $request
		 *
		 * @return mixed
		 */
		public function authenticate(ServerRequestInterface $request)
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