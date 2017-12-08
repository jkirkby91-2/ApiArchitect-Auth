<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Adapters {

		use ApiArchitect\{
			Auth\Entities\User,
			Auth\Contracts\AuthContract
		};

		use Illuminate\{
			Hashing\BcryptHasher
		};

		use Doctrine\{
			ORM\EntityNotFoundException
		};

		use Jkirkby91\{
			Boilers\RestServerBoiler\Exceptions\NotFoundHttpException
		};

		use LaravelDoctrine\{
			ORM\Auth\DoctrineUserProvider
		};

		use Symfony\{
			Component\HttpKernel\Exception\UnauthorizedHttpException
		};

		/**
		 * Class DoctrineUserAdapter
		 *
		 * Doctrine authentication driver for JWT Auth
		 *
		 * @package ApiArchitect\Auth\Adapters
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class DoctrineUserAdapter implements AuthContract
		{

			/**
			 * @var
			 */
			protected $auth;

			/**
			 * @var User $user
			 */
			protected $user;

			/**
			 * @var \LaravelDoctrine\ORM\Auth\DoctrineUserProvider $doctrineUserProvider
			 */
			protected $doctrineUserProvider;

			/**
			 * DoctrineUserAdapter constructor.
			 *
			 * @param DoctrineUserProvider $doctrineUserProvider
			 */
			public function __construct(DoctrineUserProvider $doctrineUserProvider)
			{
				$this->doctrineUserProvider = $doctrineUserProvider;
			}

			/**
			 * byCredentials()
			 * @param array $credentials
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function byCredentials(array $credentials) : User
			{
				//try get a user
				$authTarget = $this->ifFound($this->doctrineUserProvider->retrieveByCredentials($credentials));

				//validate found user
				if ($this->doctrineUserProvider->validateCredentials($authTarget,$credentials) === true){
					return $authTarget;
				} else {
					throw new NotFoundHttpException;
				}
			}

			/**
			 * byId()
			 * @param int $id
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function byId(int $id) : User
			{
				$authTarget = $this->ifFound($this->doctrineUserProvider->retrieveById($id));

				if ($authTarget instanceof User) {
					return $authTarget;
				} else {
					throw new NotFoundHttpException;
				}
			}

			/**
			 * user()
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function user() : User
			{
				return $this->user;
			}

			/**
			 * ifFound()
			 * @param \ApiArchitect\Auth\Entities\User $user
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			private function ifFound(User $user) : User
			{
				if (is_null($user) || !is_a($user, 'ApiArchitect\Auth\Entities\User')) {
					throw new NotFoundHttpException();
				} else {
					if ($user->getEnabled() != false) {
						$this->user = $user;
						return $this->user;
					} else {
						throw new UnauthorizedHttpException('User Account Has Been Banned');
					}
				}
			}
		}
	}
