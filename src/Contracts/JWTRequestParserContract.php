<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Contracts {

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		/**
		 * Interface JWTRequestParserContract
		 *
		 * @package ApiArchitect\Auth\Contracts
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		interface JWTRequestParserContract
		{

			/**
			 * Get the parser chain.
			 *
			 * @return array
			 */
			public function getChain() : array;

			/**
			 * setChain()
			 *
			 * Set the order of the parser chain.
			 *
			 * @param array $chain
			 *
			 * @return \ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function setChain(array $chain) : JWTRequestParserContract;

			/**
			 * setChainOrder()
			 *
			 * Alias for setting the order of the chain.
			 *
			 * @param array $chain
			 *
			 * @return \ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function setChainOrder(array $chain) : JWTRequestParserContract;

			/**
			 * parseToken()
			 *
			 * Iterate through the parsers and attempt to retrieve
			 * a value, otherwise return null.
			 *
			 * @return string
			 */
			public function parseToken() : string;

			/**
			 * hasToken()
			 *
			 * Check whether a token exists in the chain.
			 *
			 * @return bool
			 */
			public function hasToken() : bool ;

			/**
			 * setRequest()
			 *
			 * Set the request instance.
			 *
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function setRequest(ServerRequestInterface $request) : JWTRequestParserContract;

		}
	}
