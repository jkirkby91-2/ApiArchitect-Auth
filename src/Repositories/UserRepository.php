<?php declare(strict_types=1);

	namespace ApiArchitect\Auth\Repositories {

		use ApiArchitect\{
			Auth\Entities\User,
			Compass\Repositories\AbstractRepository
		};

		use Jkirkby91\{
			DoctrineRepositories\ResourceRepositoryTrait,
			Boilers\NodeEntityBoiler\EntityContract as Entity,
			Boilers\RepositoryBoiler\ResourceRepositoryContract
		};

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
			 * @TODO check if we access first & last name from Oauth user object as this will now not instantiate a new user
			 */
			public function findOrCreateOauthUser(User $oauthUser) : User
			{
				$userEntity = $this->findUserFromEmail($oauthUser->getEmail());

				if (!empty($userEntity) || !is_null($userEntity)){
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
			 * @param string $email
			 * @param string $firstName
			 * @param string $lastName
			 * @param string $username
			 * @param string $role
			 * @param string $password
			 * @param null   $avatar
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function findOrCreateUser(string $email, string $firstName, string $lastName, string $username, string $role, string $password, $avatar=null) : User
			{
				$userEntity = $this->findUserFromEmail($email);

				if (!empty($userEntity)){
					return $userEntity;
				} else {
					$userEntity = app()->make('apiarchitect.account.service')->createNewAccount(
						$email,
						$firstName,
						$lastName,
						$username,
						$role,
						$password,
						$avatar
					);
				};

				return $userEntity;
			}

			/**
			 * setUserAvatar()
			 * @param \ApiArchitect\Auth\Entities\User $userEntity
			 * @param string                           $avatar
			 * @param bool                             $store
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setUserAvatar(User $userEntity, string $avatar, bool $store = FALSE) : User
			{
				$imageHandler = app()->make('\Healer\Api\Services\ImageHandler\ImageHandler');

				//@TODO get avatars path from config
				$imageSecurityReport = $imageHandler->getImageSecurityHandler()->runImageSecurityTest($avatar,'avatars',$userEntity->getId());

				if ($imageSecurityReport->isFileTestResult()) {

					if ($imageHandler->getFileHandler()->moveQuarentinedImageToPublicSpace($imageSecurityReport, $userEntity->getId())) {
						$userEntity->setAvatar($imageSecurityReport->getFileName());
					}
				}

				if (!$store) {
					$userEntity = $this->update($userEntity);
				}

				return $userEntity;
			}

			/**
			 * findUserFromEmail()
			 * @param $email
			 *
			 * @return \ApiArchitect\Auth\Entities\User|null
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
			 * @return \ApiArchitect\Auth\Entities\User|null
			 */
			public function FindUserFromUserName($username)
			{
				$userEntity = $this->findOneBy(['username' => $username]);
				return $userEntity;
			}
		}
	}
