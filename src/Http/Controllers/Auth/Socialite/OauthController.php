<?php

	namespace ApiArchitect\Auth\Http\Controllers\Auth\Socialite;

	use ApiArchitect\Auth\ApiArchitectAuth;
	use Socialite;
	use Tymon\JWTAuth\JWTAuth;
	use ApiArchitect\Auth\Entities\User;
	use Laravel\Socialite\SocialiteManager;
	use Psr\Http\Message\ServerRequestInterface;
	use ApiArchitect\Auth\Contracts\SocialiteOauthContract;
	use Spatie\Fractal\ArraySerializer AS ArraySerialization;
	use Jkirkby91\LumenRestServerComponent\Libraries\ResourceResponseTrait;
	use Jkirkby91\LumenRestServerComponent\Http\Controllers\RestController;
	use Jkirkby91\Boilers\RestServerBoiler\TransformerContract AS ObjectTransformer;
	use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;
	use Zend\Diactoros\Response\JsonResponse;

	/**
	 * Class OauthController
	 *
	 * @package ApiArchitect\Auth\Http\Controllers\Auth\Socialite
	 * @author  James Kirkby <jkirkby@protonmail.ch>
	 */
	class OauthController extends RestController implements SocialiteOauthContract
	{

		use ResourceResponseTrait;

		/**
		 * @var \Laravel\Socialite\SocialiteManager
		 */
		protected $socialiteManager;

		/**
		 * @var \ApiArchitect\Auth\Repositories\UserRepository
		 */
		protected $repository;

		/**
		 * @var \ApiArchitect\Auth\ApiArchitectAuth
		 */
		protected $auth;

		/**
		 * @var \Jkirkby91\Boilers\RestServerBoiler\TransformerContract
		 */
		protected $transformer;

		/**
		 * OauthController constructor.
		 *
		 * @param \Laravel\Socialite\SocialiteManager                            $socialiteManager
		 * @param \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract $repository
		 * @param \ApiArchitect\Auth\ApiArchitectAuth                            $auth
		 * @param \Jkirkby91\Boilers\RestServerBoiler\TransformerContract        $objectTransformer
		 */
		public function __construct(SocialiteManager $socialiteManager, ResourceRepository $repository, ApiArchitectAuth $auth, ObjectTransformer $objectTransformer)
		{
			parent::__construct();
			$this->auth = $auth;
			$this->socialiteManager = $socialiteManager;
			$this->repository = $repository;
			$this->transformer = $objectTransformer;
		}

		/**
		 * redirectToProvider()
		 *
		 * Get the target redirect url
		 *
		 * @param $provider
		 *
		 * @return mixed
		 */
		public function redirectToProvider($provider)
		{
			return $this->socialiteManager->with($provider)->stateless()->redirect()->getTargetUrl();
		}

		/**
		 * handleProviderCallback()
		 *
		 * Obtain the user information from provider.  Check if the user already exists in our
		 * database by looking up their provider_id in the database.
		 * If the user exists, log them in. Otherwise, create a new user then log them in. After that
		 * redirect them to the authenticated users homepage.
		 *
		 * @param \Psr\Http\Message\ServerRequestInterface $request
		 * @param                                          $provider
		 *
		 * @return \Zend\Diactoros\Response\JsonResponse
		 * @throws \Exception
		 */
		public function handleProviderCallback(ServerRequestInterface $request, $provider) : JsonResponse
		{
			$payload = $request->getParsedBody();

			$token = $this->socialiteManager->with($provider)->stateless()->getAccessTokenResponse($payload['code']);

			$oauthUser = $this->socialiteManager->with($provider)->stateless()->userFromToken($token['access_token']);

			$userEntity = $this->repository->findOrCreateOauthUser($oauthUser);

			$providerEntity = app()
				->make('em')
				->getRepository('\ApiArchitect\Auth\Entities\Social\Provider')
				->findOneBy(['name' => $provider]);

			if(is_null($providerEntity))
			{
				throw new \Exception('Oauth Provider not found');
			}

			$socialAccountEntity = app()
				->make('em')
				->getRepository('\ApiArchitect\Auth\Entities\Social\SocialAccount')
				->findOrCreateSocialAccount($providerEntity, $oauthUser, $userEntity);

			$token = $this->auth->fromUser($userEntity);

			$resource = $this->item($token)
				->transformWith($this->transformer)
				->serializeWith($this->serializer)
				->toArray();

			return $this->showResponse($resource);
		}
	}
