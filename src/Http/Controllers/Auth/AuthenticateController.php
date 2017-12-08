<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Controllers\Auth {

		use ApiArchitect\{
			Auth\ApiArchitectAuth,
			Auth\Contracts\JWTAuthControllerContract
		};

		use Tymon\{
			JWTAuth\JWTAuth, JWTAuth\Exceptions\JWTException
		};

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use Doctrine\{
			ORM\EntityNotFoundException, Common\Collections\ArrayCollection
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

		use Zend\Diactoros\Response\{
			JsonResponse
		};

		/**
		 * Class AuthenticateController
		 *
		 * @package ApiArchitect\Auth\Http\Controllers\Auth
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class AuthenticateController extends RestController implements JWTAuthControllerContract
		{

			use ResourceResponseTrait;

			/**
			 * @var \ApiArchitect\Auth\ApiArchitectAuth|\Tymon\JWTAuth\JWTAuth $auth
			 */
			protected $auth;

			/**
			 * @var \Jkirkby91\Boilers\RestServerBoiler\TransformerContract $authTokenTransformer
			 */
			protected $authTokenTransformer;

			/**
			 * @var \Jkirkby91\Boilers\RestServerBoiler\TransformerContract $userTransformer
			 */
			protected $userTransformer;

			/**
			 * @var \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract $repository
			 */
			protected $repository;

			/**
			 * AuthenticateController constructor.
			 *
			 * @param \ApiArchitect\Auth\ApiArchitectAuth                            $auth
			 * @param \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract $repository
			 * @param \Jkirkby91\Boilers\RestServerBoiler\TransformerContract        $userTransformer
			 * @param \Jkirkby91\Boilers\RestServerBoiler\TransformerContract        $authTokenTransformer
			 */
			public function __construct(ApiArchitectAuth $auth, ResourceRepository $repository, ObjectTransformer $authTokenTransformer, ObjectTransformer $userTransformer)
			{
				parent::__construct();
				$this->auth = $auth;
				$this->repository = $repository;
				$this->userTransformer = $userTransformer;
				$this->authTokenTransformer = $authTokenTransformer;
			}

			/**
			 * authenticate()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function authenticate(ServerRequestInterface $request) : JsonResponse
			{
				try {
					if (! $this->auth->getProvider()->byCredentials($request->getParsedBody())) {
						return $this->UnauthorizedResponse();
					}
				} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
					return $this->clientErrorResponse();
				}

				try {
					$authedUser = $this->auth->fromSubject($this->auth->getProvider()->user());
				} catch (EntityNotFoundException $e) {
					return $this->notFoundResponse();
				}

				$resource = $this->item($authedUser)
					->transformWith($this->authTokenTransformer)
					->serializeWith(new ArraySerialization())
					->toArray();

				return $this->showResponse($resource);
			}

			/**
			 * logout()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function logout(ServerRequestInterface $request) : JsonResponse
			{
				try {
					$resource = $this->auth->invalidate();
				} catch (JWTException $e) {
					return $this->clientErrorResponse();
				}
				return $this->completedResponse();
			}

			/**
			 * authenticatedUser()
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function authenticatedUser() : JsonResponse
			{
				try {
					if (!$authedUser = $this->auth->parseToken()->authenticate()) {
						return $this->notFoundResponse();
					}
				} catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
					return $this->unauthorizedResponse();
				} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
					return $this->unauthorizedResponse();
				} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
					return $this->unauthorizedResponse();
				}

				$resource = $this->item($authedUser)
					->transformWith($this->userTransformer)
					->serializeWith($this->serializer)
					->toArray();

				return $this->showResponse($resource);
			}

			/**
			 * getToken()
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function getToken() : JsonResponse
			{
				$token = $this->auth->getToken();
				if (!$token) {
					return $this->unauthorizedResponse();
				}

				try {
					$refreshedToken = $this->auth->refresh($token);
				} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
					return $this->clientErrorResponse('Not able to refresh Token');
				}

				$resource = $this->item($refreshedToken)
					->transformWith($this->authTokenTransformer)
					->serializeWith(new ArraySerialization())
					->toArray();

				return $this->showResponse($resource);
			}

			/**
			 * user()
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function user() : JsonResponse
			{
				$authedUser = $this->repository->find($this->auth->getPayload()->get('sub'));

				$resource = $this->item($authedUser)
					->transformWith($this->userTransformer)
					->serializeWith($this->serializer)
					->toArray();

				return $this->showResponse($resource);
			}

			/**
			 * refresh()
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function refresh() : JsonResponse
			{
				return $this->getToken();
			}
		}
	}
