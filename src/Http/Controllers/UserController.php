<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Controllers {

		use ApiArchitect\{
			Auth\ApiArchitectAuth
		};

		use Jkirkby91\{
			Boilers\RestServerBoiler\Exceptions,
			LumenRestServerComponent\Http\Controllers\ResourceController,
			Boilers\RestServerBoiler\TransformerContract as ObjectTransformer,
			Boilers\RepositoryBoiler\ResourceRepositoryContract as ResourceRepository
		};

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use Spatie\{
			Fractal\ArraySerializer as ArraySerialization
		};

		use Zend\{
			Diactoros\Response\JsonResponse
		};

		/**
		 * Class UserController
		 *
		 * @package ApiArchitect\Auth\Http\Controllers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		final class UserController extends ResourceController
		{

			/**
			 * @var \ApiArchitect\Auth\ApiArchitectAuth $auth
			 */
			protected $auth;

			/**
			 * UserController constructor.
			 *
			 * @param \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract $repository
			 * @param \Jkirkby91\Boilers\RestServerBoiler\TransformerContract        $objectTransformer
			 * @param \ApiArchitect\Auth\ApiArchitectAuth                            $auth
			 */
			public function __construct(ResourceRepository $repository, ObjectTransformer $objectTransformer, ApiArchitectAuth $auth)
			{
				$this->auth = $auth;
				parent::__construct($repository,$objectTransformer);
			}

			/**
			 * index()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function index(ServerRequestInterface $request) : JsonResponse
			{
				$resource = $this->item($this->auth->getProvider()->user())
					->transformWith($this->transformer)
					->serializeWith(new ArraySerialization())
					->toArray();

				return $this->showResponse($resource);
			}

			/**
			 * register()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function register(ServerRequestInterface $request) : JsonResponse
			{
				return $this->store($request);
			}

			/**
			 * store()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 * @TODO hook in a method to see if we need to confirm email, current hardcoded to false
			 */
			public function store(ServerRequestInterface $request) : \Zend\Diactoros\Response\JsonResponse
			{

				$userRegDetails = $request->getParsedBody();

				$userEntity = $this->repository->findOrCreateUser(
					$userRegDetails['email'],
					$userRegDetails['firstName'],
					$userRegDetails['lastName'],
					$userRegDetails['username'],
					$userRegDetails['role'],
					$userRegDetails['password']
				);

				$token = $this->auth->fromUser($userEntity);

				$resource = $this->item($userEntity)
					->transformWith($this->transformer)
					->addMeta(['token' => $token])
					->addMeta(['confirm' => false])
					->serializeWith($this->serializer);

				return $this->createdResponse($resource);
			}

			/**
			 * update()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 * @param                                          $id
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function update(ServerRequestInterface $request, int $id) : \Zend\Diactoros\Response\JsonResponse
			{
				$userProfileDetails = $request->getParsedBody();

				try {
					if (!$data = $this->repository->findUserFromEmail($this->auth->getUser()->getEmail())) {
						throw new Exceptions\NotFoundHttpException();
					}
				} catch (Exceptions\NotFoundHttpException $exception) {
					$this->notFoundResponse();
				}

				// if (isset($userProfileDetails['roles'])) {
				//   $data = $data->addRoles($userProfileDetails['roles']);
				// }

				if (isset($userProfileDetails['name'])) {
					$data = $data->setName($userProfileDetails['name']);
				}

				if (isset($userProfileDetails['username'])) {
					$data = $data->setUserName($userProfileDetails['username']);
				}

				if (isset($userProfileDetails['email'])) {
					$data = $data->setEmail($userProfileDetails['email']);
				}

				//@TODO Create a new route for password resets that does some validation middleware
				if (isset($userProfileDetails['password'])) {

					try {
						if ($userProfileDetails['password'] !== $userProfileDetails['password_confirmation']) {
							throw new Exceptions\UnprocessableEntityException('Passwords do not match');
						}
					} catch (Exceptions\UnprocessableEntityException $exception) {
						$this->clientErrorResponse($exception->getMessage());
					}

					$data = $data->setPassword($userProfileDetails['password']);
				}

				if (isset($userProfileDetails['permissions'])) {
					$data = $data->setPermissions($userProfileDetails['permissions']);
				}

				$this->repository->update($data);

				$resource = $this->item($data)
					->transformWith($this->transformer)
					->serializeWith($this->serializer)
					->toArray();

				return $this->createdResponse($resource);
			}

			/**
			 * checkUniqueEmail()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function checkUniqueEmail(ServerRequestInterface $request) : JsonResponse
			{
				$emailTarget = $request->getParsedBody();

				if (!in_array('email', $emailTarget))
				{
					return $this->clientErrorResponse();
				}

				$userEntity = $this->repository->findUserFromEmail($emailTarget['email']);

				if (is_null($userEntity)) {
					return $this->completedResponse();
				} else {
					return $this->notFoundResponse();
				}
			}

			/**
			 * checkUniqueUserName()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function checkUniqueUserName(ServerRequestInterface $request) : JsonResponse
			{
				$userNameTarget = $request->getParsedBody();

				if (!in_array('username', $userNameTarget)) {
					return $this->clientErrorResponse('no username defined');
				}

				$userEntity = $this->repository->FindUserFromUserName($userNameTarget['username']);

				if (is_null($userEntity)) {
					return $this->completedResponse();
				} else {
					return $this->notFoundResponse();
				}
			}
		}
	}
