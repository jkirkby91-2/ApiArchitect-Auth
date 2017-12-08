<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Contracts {

		use ApiArchitect\{
			Auth\Entities\Social\Provider
		};

		use Psr\{
			Http\Message\ServerRequestInterface
		};
		use Zend\Diactoros\Response\JsonResponse;

		/**
		 * Interface SocialiteOauthContract
		 *
		 * @package ApiArchitect\Auth\Contracts
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		interface SocialiteOauthContract
		{

			/**
			 * redirectToProvider()
			 * @param \ApiArchitect\Auth\Entities\Social\Provider $provider
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function redirectToProvider(Provider $provider) : JsonResponse;

			/**
			 * handleProviderCallback()
			 *
			 * Obtain the user information from provider.  Check if the user already exists in our
			 * database by looking up their provider_id in the database.
			 * If the user exists, log them in. Otherwise, create a new user then log them in. After that
			 * redirect them to the authenticated users homepage.
			 *
			 * @param \Psr\Http\Message\ServerRequestInterface    $request
			 * @param \ApiArchitect\Auth\Entities\Social\Provider $provider
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function handleProviderCallback(ServerRequestInterface $request,Provider $provider) : JsonResponse;

		}
	}
