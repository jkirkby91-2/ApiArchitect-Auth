<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Contracts {

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		/**
		 * Interface JWTParserContract
		 *
		 * @package ApiArchitect\Auth\Contracts
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		interface JWTParserContract
		{
			/**
			 * Parse the request.
			 *
			 * @param  \Psr\Http\Message\ServerRequestInterface  $request
			 *
			 * @return null|string
			 */
			public function parse(ServerRequestInterface $request) : string;
		}
	}
