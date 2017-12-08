<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth {

		use Psr\Http\Message\ServerRequestInterface;
		use Symfony\Component\Console\Exception\LogicException;
		use Tymon\{
			JWTAuth\Blacklist, JWTAuth\Factory, JWTAuth\Manager, JWTAuth\Contracts\Providers\Auth, JWTAuth\Payload, JWTAuth\Support\CustomClaims, JWTAuth\Token
		};

		use ApiArchitect\{
			Auth\Contracts\JWTRequestParserContract, Auth\Entities\User
		};

		/**
		 * Class ApiArchitectAuth
		 *
		 * @package ApiArchitect\Auth
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class ApiArchitectAuth
		{
			use CustomClaims;

			/**
			 * @var \Tymon\JWTAuth\Contracts\Providers\Auth $provider
			 */
			protected $provider;

			/**
			 * The authentication manager.
			 *
			 * @var \Tymon\JWTAuth\Manager $manager
			 */
			protected $manager;

			/**
			 * @var \ApiArchitect\Auth\Contracts\JWTParserContract|\ApiArchitect\Auth\Contracts\JWTRequestParserContract $parser
			 */
			protected $parser;

			/**
			 * @var \ApiArchitect\Auth\Entities\User $user
			 */
			protected $user;

			/**
			 * ApiArchitectAuth constructor.
			 *
			 * @param \Tymon\JWTAuth\Manager                                $manager
			 * @param \Tymon\JWTAuth\Contracts\Providers\Auth               $auth
			 * @param \ApiArchitect\Auth\Contracts\JWTRequestParserContract $parser
			 */
			public function __construct(Manager $manager, Auth $auth, JWTRequestParserContract $parser)
			{
				$this->manager = $manager;
				$this->provider = $auth;
				$this->parser = $parser;
				$this->user = $this->provider->user();
			}

			/**
			 * getParser()
			 * @return \ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function getParser() : JWTRequestParserContract
			{
				return $this->parser;
			}

			/**
			 * getProvider()
			 * @return \Tymon\JWTAuth\Contracts\Providers\Auth
			 */
			public function getProvider()
			{
				return $this->provider;
			}

			/**
			 * getManager()
			 * @return \Tymon\JWTAuth\Manager
			 */
			public function getManager()
			{
				return $this->manager;
			}

			/**
			 * getUser()
			 * @return mixed
			 */
			public function getUser() : User
			{
				return $this->user;
			}

			/**
			 * fromSubject()
			 * @param \ApiArchitect\Auth\Entities\User $subject
			 *
			 * @return string
			 */
			public function fromSubject(User $subject) : string
			{
				$payload = $this->makePayload($subject);

				return $this->manager->encode($payload)->get();
			}

			/**
			 * fromUser()
			 * @param \ApiArchitect\Auth\Entities\User $user
			 *
			 * @return string
			 */
			public function fromUser(User $user) : string
			{
				return $this->fromSubject($user);
			}

			/**
			 * refresh()
			 * @param bool $forceForever
			 * @param bool $resetClaims
			 *
			 * @return string
			 */
			public function refresh(bool $forceForever = false, bool $resetClaims = false) : string
			{
				$this->requireToken();

				return $this->manager->customClaims($this->getCustomClaims())
					->refresh($this->token, $forceForever, $resetClaims)
					->get();
			}

			/**
			 * invalidate()
			 * @param bool $forceForever
			 *
			 * @return $this
			 * @throws \Tymon\JWTAuth\Exceptions\JWTException
			 */
			public function invalidate(bool $forceForever = false) : ApiArchitectAuth
			{
				$this->requireToken();

				$this->manager->invalidate($this->token, $forceForever);

				return $this;
			}

			/**
			 * checkOrFail()
			 * @return \Tymon\JWTAuth\Payload
			 */
			public function checkOrFail() : Payload
			{
				return $this->getPayload();
			}

			/**
			 * check()
			 * @return bool
			 */
			public function check() : bool
			{
				try {
					$this->checkOrFail();
				} catch (JWTException $e) {
					return false;
				}

				return true;
			}

			/**
			 * getToken()
			 * @return \Tymon\JWTAuth\Token
			 */
			public function getToken() : Token
			{
				if (! $this->token) {
					try {
						$this->parseToken();
					} catch (JWTException $e) {
						throw new \LogicException();
					}
				}

				return $this->token;
			}

			/**
			 * parseToken()
			 * @return \ApiArchitect\Auth\ApiArchitectAuth
			 */
			public function parseToken() : ApiArchitectAuth
			{
				if (! $token = $this->parser->parseToken()) {
					throw new JWTException('The token could not be parsed from the request');
				}

				return $this->setToken($token);
			}

			/**
			 * getPayload()
			 * @return \Tymon\JWTAuth\Payload
			 * @throws \Tymon\JWTAuth\Exceptions\TokenBlacklistedException
			 */
			public function getPayload() : Payload
			{
				$this->requireToken();

				return $this->manager->decode($this->token);
			}

			/**
			 * payload()
			 * @return \Tymon\JWTAuth\Payload
			 * @throws \Tymon\JWTAuth\Exceptions\TokenBlacklistedException
			 */
			public function payload() : Payload
			{
				return $this->getPayload();
			}

			/**
			 * getClaim()
			 * @param string $claim
			 *
			 * @return \Tymon\JWTAuth\Payload
			 * @throws \Tymon\JWTAuth\Exceptions\TokenBlacklistedException
			 */
			public function getClaim(string $claim) : Payload
			{
				return $this->payload()->get($claim);
			}

			/**
			 * makePayload()
			 * @param \ApiArchitect\Auth\Entities\User $subject
			 *
			 * @return \Tymon\JWTAuth\Payload
			 */
			public function makePayload(User $subject) : Payload
			{
				return $this->factory()->customClaims($this->getClaimsArray($subject))->make();
			}

			/**
			 * getClaimsArray()
			 * @param \ApiArchitect\Auth\Entities\User $subject
			 *
			 * @return array
			 */
			protected function getClaimsArray(User $subject) : array
			{
				return array_merge(
					['sub' => $subject->getJWTIdentifier()],
					$this->customClaims, // custom claims from inline setter
					$subject->getJWTCustomClaims() // custom claims from JWTSubject method
				);
			}

			/**
			 * setToken()
			 * @param $token
			 *
			 * @return \ApiArchitect\Auth\ApiArchitectAuth
			 */
			public function setToken($token) : ApiArchitectAuth
			{
				$this->token = $token instanceof Token ? $token : new Token($token);

				return $this;
			}

			/**
			 * unsetToken()
			 * @return \ApiArchitect\Auth\ApiArchitectAuth
			 */
			public function unsetToken() : ApiArchitectAuth
			{
				$this->token = null;

				return $this;
			}

			/**
			 * requireToken()
			 */
			protected function requireToken()
			{
				if (! $this->token) {
					throw new \LogicException('A token is required');
				}
			}

			/**
			 * setRequest()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \ApiArchitect\Auth\ApiArchitectAuth
			 */
			public function setRequest(ServerRequestInterface $request) :  ApiArchitectAuth
			{
				$this->parser->setRequest($request);

				return $this;
			}

			/**
			 * Get the Manager instance.
			 *
			 * @return \Tymon\JWTAuth\Manager
			 */
			public function manager()
			{
				return $this->manager;
			}

			/**
			 * parser()
			 * @return \ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function parser() : JWTRequestParserContract
			{
				return $this->parser;
			}

			/**
			 * Get the Payload Factory.
			 *
			 * @return \Tymon\JWTAuth\Factory
			 */
			public function factory() : Factory
			{
				return $this->manager->getPayloadFactory();
			}

			/**
			 * Get the Blacklist.
			 *
			 * @return \Tymon\JWTAuth\Blacklist
			 */
			public function blacklist() : Blacklist
			{
				return $this->manager->getBlacklist();
			}

			/**
			 * Magically call the JWT Manager.
			 *
			 * @param  string  $method
			 * @param  array  $parameters
			 *
			 * @throws \BadMethodCallException
			 *
			 * @return mixed
			 */
			public function __call($method, $parameters)
			{
				if (method_exists($this->manager, $method)) {
					return call_user_func_array([$this->manager, $method], $parameters);
				}

				throw new BadMethodCallException("Method [$method] does not exist.");
			}
		}
	}
