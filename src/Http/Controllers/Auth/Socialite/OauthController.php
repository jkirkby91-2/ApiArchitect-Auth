<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Controllers\Auth\Socialite {

		use ApiArchitect\{
			Auth\Entities\User,
			Auth\ApiArchitectAuth,
			Auth\Entities\Social\Provider,
			Auth\Contracts\SocialiteOauthContract
		};

		use Doctrine\{
			ORM\EntityNotFoundException
		};

		use Tymon\{
			JWTAuth\JWTAuth
		};

		use Laravel\{
			Socialite\SocialiteManager
		};

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use Spatie\{
			Fractal\ArraySerializer as ArraySerialization
		};

		use Jkirkby91\{
			LumenRestServerComponent\Libraries\ResourceResponseTrait,
			LumenRestServerComponent\Http\Controllers\RestController,
			Boilers\RestServerBoiler\TransformerContract as ObjectTransformer,
			Boilers\RepositoryBoiler\ResourceRepositoryContract as ResourceRepository
		};

		use Zend\{
			Diactoros\Response\JsonResponse
		};

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
			 * @var \Laravel\Socialite\SocialiteManager $socialiteManager
			 */
			protected $socialiteManager;

			/**
			 * @var \ApiArchitect\Auth\Repositories\UserRepository $repository
			 */
			protected $repository;

			/**
			 * @var \ApiArchitect\Auth\ApiArchitectAuth $auth
			 */
			protected $auth;

			/**
			 * @var \Jkirkby91\Boilers\RestServerBoiler\TransformerContract $transformer
			 */
			protected $transformer;

			/**
			 * OauthController constructor.
			 *
			 * @param \ApiArchitect\Auth\ApiArchitectAuth                            $auth
			 * @param \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract $repository
			 * @param \Laravel\Socialite\SocialiteManager                            $socialiteManager
			 * @param \Jkirkby91\Boilers\RestServerBoiler\TransformerContract        $objectTransformer
			 */
			public function __construct(SocialiteManager $socialiteManager, ResourceRepository $repository, ApiArchitectAuth $auth, ObjectTransformer $objectTransformer)
			{
				parent::__construct();
				$this->auth = $auth;
				$this->repository = $repository;
				$this->transformer = $objectTransformer;
				$this->socialiteManager = $socialiteManager;
			}

			/**
			 * redirectToProvider()
			 * @param \ApiArchitect\Auth\Entities\Social\Provider $provider
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function redirectToProvider(Provider $provider) : JsonResponse
			{
				$redirectUri = $this->socialiteManager->with($provider)->stateless()->redirect()->getTargetUrl();

				$resource = $this->item($redirectUri)
					->serializeWith($this->serializer)
					->toArray();

				return $this->showResponse($resource);
			}

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
			 * @throws \Doctrine\ORM\EntityNotFoundException
			 */
			public function handleProviderCallback(ServerRequestInterface $request, Provider $provider) : JsonResponse
			{
				$payload = $request->getParsedBody();

				$token = $this->socialiteManager->with($provider)->stateless()->getAccessTokenResponse($payload['code']);

				$oauthUser = $this->socialiteManager->with($provider)->stateless()->userFromToken($token['access_token']);

				$userEntity = $this->repository->findOrCreateOauthUser($oauthUser);

				$providerEntity = app()
					->make('em')
					->getRepository('\ApiArchitect\Auth\Entities\Social\Provider')
					->findOneBy(['name' => $provider]);

				if (is_null($providerEntity) || !($providerEntity instanceOf Provider))
				{
					throw new EntityNotFoundException('Oauth Provider not found');
				}

//			$socialAccountEntity = app()
//				->make('em')
//				->getRepository('\ApiArchitect\Auth\Entities\Social\SocialAccount')
//				->findOrCreateSocialAccount($providerEntity, $oauthUser, $userEntity);

				$token = $this->auth->fromUser($userEntity);

				$resource = $this->item($token)
					->transformWith($this->transformer)
					->serializeWith($this->serializer)
					->toArray();

				return $this->showResponse($resource);
			}
		}
	}
