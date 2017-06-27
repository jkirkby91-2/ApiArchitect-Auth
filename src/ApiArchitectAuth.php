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

	use Tymon\JWTAuth\JWT;
	use Tymon\JWTAuth\Manager;
	use Tymon\JWTAuth\Contracts\Providers\Auth;
	use ApiArchitect\Auth\Contracts\JWTRequestParserContract;

	/**
	 * Class ApiArchitectAuth
	 *
	 * @package ApiArchitect\Auth
	 * @author  James Kirkby <jkirkby@protonmail.ch>
	 */
	class ApiArchitectAuth extends JWT
	{
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
		 * ApiArchitectAuth constructor.
		 *
		 * @param \Tymon\JWTAuth\Manager                         $manager
		 * @param \Tymon\JWTAuth\Contracts\Providers\Auth        $auth
		 * @param \ApiArchitect\Auth\Contracts\JWTParserContract $parser
		 */
		public function __construct(Manager $manager, Auth $auth, JWTRequestParserContract $parser)
		{
			$this->provider = $auth;
			$this->manager = $manager;
			$this->parser = $parser;
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
		
		public function getManager()
		{
			return $this->manager;
		}
	}