<?php declare(strict_types=1);

	namespace ApiArchitect\Auth\Services {

		use ApiArchitect\{
			Auth\Entities\User
		};

		use Jkirkby91\{
			Boilers\RepositoryBoiler\ResourceRepositoryContract as ResourceRepository
		};

		/**
		 * Class AccountService
		 *
		 * @package ApiArchitect\Auth\Services
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class AccountService
		{

			/**
			 * @var \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract
			 */
			protected $repository;

			/**
			 * AccountService constructor.
			 *
			 * @param \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract $repository
			 */
			public function __Construct(ResourceRepository $repository)
			{
				$this->repository = $repository;
			}

			/**
			 * createNewAccount()
			 * @param      $email
			 * @param      $firstName
			 * @param      $lastName
			 * @param      $username
			 * @param null $role
			 * @param null $password
			 * @param null $avatar
			 *
			 * @return $this|\ApiArchitect\Auth\Entities\User|mixed
			 */
			public function createNewAccount(string $email, string $firstName, string $lastName, string $username, $role=null, $password=null, $avatar=null) : User
			{
				$userEntity = new User($email, $username, $firstName, $lastName);

				if ($password === null)
				{
					$userEntity->setOTP(1);
					$userEntity->setPassword(md5(microtime(true).$email.$firstName.$lastName));
				} else {
					$userEntity = $userEntity->setPassword(app()->make('hash')->make($password));
				}

				if ($role === null)
				{
					$role = 'user';
				}

				$roleEntity = app()
					->make('em')
					->getRepository('\ApiArchitect\Auth\Entities\Role')
					->findOneBy(['name' => $role]);

				if (is_null($roleEntity)) {
					throw new \Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnprocessableEntityException('target role not found');
				}

				$userEntity->addRoles($roleEntity);

				if ($avatar !== null)
				{
					$userEntity = $this->repository->setUserAvatar($userEntity, $avatar);
				}

				$userEntity = $this->repository->store($userEntity);

				return $userEntity;
			}
		}
	}