<?php
	
	/**
	 * Project: digitalnomads-api
	 *
	 * @author: James Kirkby <jkirkby@protonmail.ch>
	 * Copyright: Blacksands.Network
	 * File: ApiArchitectAuth.php
	 * Date: 27/06/2017
	 * Time: 22:17
	 */

	namespace ApiArchitect\Auth;

	use Tymon\JWTAuth\Manager;
	use Tymon\JWTAuth\Contracts\Providers\Auth;
	use ApiArchitect\Auth\Contracts\JWTRequestParserContract;
	use Tymon\JWTAuth\Support\CustomClaims;

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
		 * @var \Tymon\JWTAuth\Contracts\Providers\Auth
		 */
		protected $provider;
		
		/**
		 * The authentication manager.
		 *
		 * @var \Tymon\JWTAuth\Manager
		 */
		protected $manager;

		/**
		 * @var \ApiArchitect\Auth\Contracts\JWTParserContract|\ApiArchitect\Auth\Contracts\JWTRequestParserContract
		 */
		protected $parser;

		/**
		 * @var mixed
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
			$this->provider = $auth;
			$this->manager = $manager;
			$this->parser = $parser;
			$this->user = $this->provider->user();
		}

		/**
		 * getParser()
		 * @return \ApiArchitect\Auth\Contracts\JWTParserContract|\ApiArchitect\Auth\Contracts\JWTRequestParserContract
		 */
		public function getParser() {
			return $this->parser;
		}

		/**
		 * getProvider()
		 * @return \Tymon\JWTAuth\Contracts\Providers\Auth
		 */
		public function getProvider() {
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
		public function getUser()
		{
			return $this->user;
		}

		/**
		 * Generate a token for a given subject.
		 *
		 * @param  \Tymon\JWTAuth\Contracts\JWTSubject  $subject
		 *
		 * @return string
		 */
		public function fromSubject($subject)
		{
			$payload = $this->makePayload($subject);

			return $this->manager->encode($payload)->get();
		}

		/**
		 * Alias to generate a token for a given user.
		 *
		 * @param  \Tymon\JWTAuth\Contracts\JWTSubject  $user
		 *
		 * @return string
		 */
		public function fromUser($user)
		{
			return $this->fromSubject($user);
		}

		/**
		 * Refresh an expired token.
		 *
		 * @param  bool  $forceForever
		 * @param  bool  $resetClaims
		 *
		 * @return string
		 */
		public function refresh($forceForever = false, $resetClaims = false)
		{
			$this->requireToken();

			return $this->manager->customClaims($this->getCustomClaims())
				->refresh($this->token, $forceForever, $resetClaims)
				->get();
		}

		/**
		 * Invalidate a token (add it to the blacklist).
		 *
		 * @param  bool  $forceForever
		 *
		 * @return $this
		 */
		public function invalidate($forceForever = false)
		{
			$this->requireToken();

			$this->manager->invalidate($this->token, $forceForever);

			return $this;
		}

		/**
		 * Alias to get the payload, and as a result checks that
		 * the token is valid i.e. not expired or blacklisted.
		 *
		 * @throws \Tymon\JWTAuth\Exceptions\JWTException
		 *
		 * @return \Tymon\JWTAuth\Payload
		 */
		public function checkOrFail()
		{
			return $this->getPayload();
		}

		/**
		 * Check that the token is valid.
		 *
		 * @return bool
		 */
		public function check()
		{
			try {
				$this->checkOrFail();
			} catch (JWTException $e) {
				return false;
			}

			return true;
		}

		/**
		 * Get the token.
		 *
		 * @return \Tymon\JWTAuth\Token|false
		 */
		public function getToken()
		{
			if (! $this->token) {
				try {
					$this->parseToken();
				} catch (JWTException $e) {
					return false;
				}
			}

			return $this->token;
		}

		/**
		 * Parse the token from the request.
		 *
		 * @throws \Tymon\JWTAuth\Exceptions\JWTException
		 *
		 * @return $this
		 */
		public function parseToken()
		{
			if (! $token = $this->parser->parseToken()) {
				throw new JWTException('The token could not be parsed from the request');
			}

			return $this->setToken($token);
		}

		/**
		 * Get the raw Payload instance.
		 *
		 * @return \Tymon\JWTAuth\Payload
		 */
		public function getPayload()
		{
			$this->requireToken();

			return $this->manager->decode($this->token);
		}

		/**
		 * Alias for getPayload().
		 *
		 * @return \Tymon\JWTAuth\Payload
		 */
		public function payload()
		{
			return $this->getPayload();
		}

		/**
		 * Convenience method to get a claim value.
		 *
		 * @param  string  $claim
		 *
		 * @return mixed
		 */
		public function getClaim($claim)
		{
			return $this->payload()->get($claim);
		}

		/**
		 * Create a Payload instance.
		 *
		 * @param  \Tymon\JWTAuth\Contracts\JWTSubject  $subject
		 *
		 * @return \Tymon\JWTAuth\Payload
		 */
		public function makePayload( $subject)
		{
			return $this->factory()->customClaims($this->getClaimsArray($subject))->make();
		}

		/**
		 * Build the claims array and return it.
		 *
		 * @param  \Tymon\JWTAuth\Contracts\JWTSubject  $subject
		 *
		 * @return array
		 */
		protected function getClaimsArray( $subject)
		{
			return array_merge(
				['sub' => $subject->getJWTIdentifier()],
				$this->customClaims, // custom claims from inline setter
				$subject->getJWTCustomClaims() // custom claims from JWTSubject method
			);
		}

		/**
		 * Set the token.
		 *
		 * @param  \Tymon\JWTAuth\Token|string  $token
		 *
		 * @return $this
		 */
		public function setToken($token)
		{
			$this->token = $token instanceof Token ? $token : new Token($token);

			return $this;
		}

		/**
		 * Unset the current token.
		 *
		 * @return $this
		 */
		public function unsetToken()
		{
			$this->token = null;

			return $this;
		}

		/**
		 * Ensure that a token is available.
		 *
		 * @throws \Tymon\JWTAuth\Exceptions\JWTException
		 *
		 * @return void
		 */
		protected function requireToken()
		{
			if (! $this->token) {
				throw new JWTException('A token is required');
			}
		}

		/**
		 * Set the request instance.
		 *
		 * @param  \Illuminate\Http\Request  $request
		 *
		 * @return $this
		 */
		public function setRequest(Request $request)
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
		 * Get the Parser instance.
		 *
		 * @return \Tymon\JWTAuth\Http\Parser\Parser
		 */
		public function parser()
		{
			return $this->parser;
		}

		/**
		 * Get the Payload Factory.
		 *
		 * @return \Tymon\JWTAuth\Factory
		 */
		public function factory()
		{
			return $this->manager->getPayloadFactory();
		}

		/**
		 * Get the Blacklist.
		 *
		 * @return \Tymon\JWTAuth\Blacklist
		 */
		public function blacklist()
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