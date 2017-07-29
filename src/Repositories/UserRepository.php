<?php

	namespace ApiArchitect\Auth\Repositories;

	use ApiArchitect\Auth\Entities\User;
	use ApiArchitect\Compass\Repositories\AbstractRepository;
	use Jkirkby91\DoctrineRepositories\ResourceRepositoryTrait;
	use Jkirkby91\Boilers\NodeEntityBoiler\EntityContract AS Entity;
	use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract;

	/**
	 * Class UserRepository
	 *
	 * @package ApiArchitect\Auth\Repositories
	 * @author  James Kirkby <jkirkby@protonmail.ch>
	 */
	class UserRepository extends AbstractRepository implements ResourceRepositoryContract
	{
		use ResourceRepositoryTrait;

		/**
		 * findOrCreateOauthUser()
		 * @param $oauthUser
		 *
		 * @return \ApiArchitect\Auth\Entities\User
		 */
		public function findOrCreateOauthUser($oauthUser) : User
		{
			$userEntity = $this->findUserFromEmail($oauthUser->getEmail());

			if(!empty($userEntity) || !is_null($userEntity)){
				return $userEntity;
			} else {
				$userEntity = app()->make('apiarchitect.account.service')->createNewAccount(
					$oauthUser->getEmail(),
					$oauthUser->getName(),
					$oauthUser->getNickName(),
					null,
					null,
					$oauthUser->getAvatar()
				);
			};

			return $userEntity;
		}

		/**
		 * findOrCreateUser()
		 * @param      $email
		 * @param      $name
		 * @param      $username
		 * @param      $role
		 * @param      $password
		 * @param null $avatar
		 *
		 * @return \ApiArchitect\Auth\Entities\User
		 */
		public function findOrCreateUser($email,$name,$username,$role,$password,$avatar=null) : User
		{
			$userEntity = $this->findUserFromEmail($email);

			if(!empty($userEntity)){
				return $userEntity;
			} else {
				$userEntity = app()->make('apiarchitect.account.service')->createNewAccount(
					$email,
					$name,
					$username,
					$role,
					$password,
					$avatar
				);
			};

			return $userEntity;
		}

		/**
		 * findUserFromEmail()
		 * @param $email
		 *
		 * @return null|object
		 */
		public function findUserFromEmail($email)
		{
			$userEntity = $this->findOneBy(['email' => $email]);
			return $userEntity;
		}

		/**
		 * FindUserFromUserName()
		 * @param $username
		 *
		 * @return \ApiArchitect\Auth\Entities\User
		 */
		public function FindUserFromUserName($username) : User
		{
			$userEntity = $this->findOneBy(['username' => $username]);
			return $userEntity;
		}
	}
