<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Repositories {

		use ApiArchitect\{
			Auth\Entities\User,
			Auth\Entities\Social\Provider,
			Auth\Entities\Social\SocialAccount
		};

		use Jkirkby91\{
			DoctrineRepositories\ResourceRepositoryTrait,
			Boilers\RepositoryBoiler\ResourceRepositoryContract,
			LumenDoctrineComponent\Repositories\LumenDoctrineEntityRepository
		};

		/**
		 * Class SocialAccountRepository
		 *
		 * @package ApiArchitect\Auth\Repositories
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class SocialAccountRepository extends LumenDoctrineEntityRepository implements ResourceRepositoryContract
		{
			use ResourceRepositoryTrait;

			/**
			 * findOrCreateSocialAccount()
			 * @param \ApiArchitect\Auth\Entities\Social\Provider $provider
			 * @param \ApiArchitect\Auth\Entities\User            $oauthUser
			 * @param \ApiArchitect\Auth\Entities\User            $user
			 *
			 * @return \ApiArchitect\Auth\Entities\Social\SocialAccount
			 */
			public function findOrCreateSocialAccount(Provider $provider, User $oauthUser, User $user) : SocialAccount
			{
				$socialAccountEntity = $this->findOneBy(['provider' => $provider->getId(),'providerUid' => $oauthUser->getId()]);

				if (!empty($socialAccountEntity)){
					return $socialAccountEntity;
				} else {

					$socialAccountEntity = new SocialAccount($provider,$oauthUser,$user);
					$socialAccountEntity = $this->store($socialAccountEntity);

					$user->addSocialAccount($socialAccountEntity);

					$user = app()->make('em')
						->getRepository('\ApiArchitect\Auth\Entities\User')
						->update($user);
				}
				return $socialAccountEntity;
			}
		}
	}
