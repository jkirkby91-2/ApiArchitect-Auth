<?php

	namespace ApiArchitect\Auth\Http\Controllers\Auth;

	use ApiArchitect\Auth\ApiArchitectAuth;
	use Tymon\JWTAuth\JWTAuth;
	use Doctrine\ORM\EntityNotFoundException;
	use Tymon\JWTAuth\Exceptions\JWTException;
	use Psr\Http\Message\ServerRequestInterface;
	use Doctrine\Common\Collections\ArrayCollection;
	use Spatie\Fractal\ArraySerializer AS ArraySerialization;
	use ApiArchitect\Auth\Contracts\JWTAuthControllerContract;
	use Jkirkby91\LumenRestServerComponent\Libraries\ResourceResponseTrait;
	use Jkirkby91\LumenRestServerComponent\Http\Controllers\RestController;
	use Jkirkby91\Boilers\RestServerBoiler\TransformerContract AS ObjectTransformer;
	use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;
	use Zend\Diactoros\Response\JsonResponse;

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
		 * @var \ApiArchitect\Auth\ApiArchitectAuth|\Tymon\JWTAuth\JWTAuth
		 */
		protected $auth;

		/**
		 * @var \Jkirkby91\Boilers\RestServerBoiler\TransformerContract
		 */
		protected $authTokenTransformer;

		/**
		 * @var \Jkirkby91\Boilers\RestServerBoiler\TransformerContract
		 */
		protected $userTransformer;

		/**
		 * @var \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract
		 */
		protected $repository;

		/**
		 * AuthenticateController constructor.
		 *
		 * @param \ApiArchitect\Auth\ApiArchitectAuth                            $auth
		 * @param \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract $repository
		 * @param \Jkirkby91\Boilers\RestServerBoiler\TransformerContract        $authTokenTransformer
		 * @param \Jkirkby91\Boilers\RestServerBoiler\TransformerContract        $userTransformer
		 */
		public function __construct(ApiArchitectAuth $auth, ResourceRepository $repository, ObjectTransformer $authTokenTransformer, ObjectTransformer $userTransformer)
		{
			parent::__construct();
			$this->auth = $auth;
			$this->repository = $repository;
			$this->authTokenTransformer = $authTokenTransformer;
			$this->userTransformer = $userTransformer;
		}

		/**
		 * @param ServerRequestInterface $request
		 * @return mixed|\Symfony\Component\HttpFoundation\Response
		 */
		public function authenticate(ServerRequestInterface $request)
		{

			try {
				if (! $this->auth->getProvider()->byCredentials($request->getParsedBody())) {
					return $this->UnauthorizedResponse();
				}
			} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
				return $this->clientErrorResponse();
			} catch (EntityNotFoundException $e) {
				return $this->notFoundResponse();
			}

			$resource = $this->item($this->auth->fromSubject($this->auth->getProvider()->user()))
				->transformWith($this->authTokenTransformer)
				->serializeWith(new ArraySerialization())
				->toArray();

			return $this->showResponse($resource);
		}

		/**
		 * @param ServerRequestInterface $request
		 * @return bool|\Zend\Diactoros\Response\JsonResponse
		 */
		public function logout(ServerRequestInterface $request)
		{
			$this->token = $request->getParsedBody();

			try {
				$resource = $this->auth->invalidate();
			} catch (JWTException $e) {
				return $this->clientErrorResponse();
			}
			return $resource;
		}

		/**
		 * authenticatedUser()
		 * @return \Zend\Diactoros\Response\JsonResponse
		 */
		public function authenticatedUser() : JsonResponse
		{
			try {
				if (!$this->user = $this->auth->parseToken()->authenticate()) {
					return response()->json(['user_not_found'], 404);
				}
				//@TODO hook into response trait
			} catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
				return response()->json(['token_expired'], $e->getStatusCode());
			} catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
				return response()->json(['token_invalid'], $e->getStatusCode());
			} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
				return response()->json(['token_absent'], $e->getStatusCode());
			}

			$user = $this->item($this->user)
				->transformWith($this->userTransformer)
				->serializeWith(new ArraySerialization())
				->toArray();

			return $this->showResponse($user);
		}

		/**
		 * getToken()
		 * @return \Zend\Diactoros\Response\JsonResponse
		 */
		public function getToken() : JsonResponse
		{
			$token = $this->auth->getToken();
			if (!$token) {
				return $this->response->errorMethodNotAllowed('Token not provided');
			}

			try {
				$refreshedToken = $this->auth->refresh($token);
			} catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
				return $this->clientErrorResponse('Not able to refresh Token');
			}

			$token = $this->item($refreshedToken)
				->transformWith($this->authTokenTransformer)
				->serializeWith(new ArraySerialization())
				->toArray();

			return $this->showResponse($token);
		}

		/**
		 * user()
		 * @return \Zend\Diactoros\Response\JsonResponse
		 */
		public function user() : JsonResponse
		{
			$this->user = $this->repository->find($this->auth->getPayload()->get('sub'));

			$user = $this->item($this->user)
				->transformWith($this->userTransformer)
				->serializeWith(new ArraySerialization())
				->toArray();

			return $this->showResponse($user);
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
