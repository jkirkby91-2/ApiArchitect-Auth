<?php

namespace ApiArchitect\Auth\Repositories;

use ApiArchitect\Auth\Entities\User;
use ApiArchitect\Auth\Entities\Social\Provider;
use ApiArchitect\Auth\Entities\Social\SocialAccount;
use Jkirkby91\LumenDoctrineComponent\Repositories\LumenDoctrineEntityRepository;

/**
 * Class ProviderRepository
 *
 * @package ApiArchitect\Auth\Repositories
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class SocialAccountRepository extends LumenDoctrineEntityRepository implements \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract
{
    use \Jkirkby91\DoctrineRepositories\ResourceRepositoryTrait;

    public function findOrCreateSocialAccount(Provider $provider, $oauthUser,User $user)
    {
      $socialAccountEntity = $this->findOneBy(['provider' => $provider->getId(),'providerUid' => $oauthUser->getId()]);

      if(!empty($socialAccountEntity)){
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
