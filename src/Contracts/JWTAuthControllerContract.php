<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Contracts {

		use ApiArchitect\Auth\Entities\User;
		use Psr\{
			Http\Message\ServerRequestInterface
		};
		use Zend\Diactoros\Response\JsonResponse;

		/**
		 * Interface JWTAuthControllerContract
		 *
		 * @package ApiArchitect\Auth\Contracts
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		interface JWTAuthControllerContract
		{
			/**
			 * authenticate()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return mixed
			 */
			public function authenticate(ServerRequestInterface $request) : JsonResponse;

			/**
			 * logout()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function logout(ServerRequestInterface $request) : JsonResponse;

			/**
			 * authenticatedUser()
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function authenticatedUser() : JsonResponse;

			/**
			 * getToken()
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function getToken() : JsonResponse;
		}
	}
