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
 * @package  ApiArchitect\Auth\Repositories\UserRepository
 * @author James Kirkby <me@jameskirkby.com>
 */
class UserRepository extends AbstractRepository implements ResourceRepositoryContract
{
    use ResourceRepositoryTrait;

    public function findOrCreateOauthUser($oauthUser)
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

    public function findOrCreateUser($email,$name,$username,$role,$password,$avatar=null)
    {
      $userEntity = $this->findUserFromEmail($oauthUser->getEmail());

      if(!empty($userEntity)){
        return $userEntity;
      } else {
        $userEntity = app()->make('apiarchitect.account.service')->createNewAccount(
          $email,
          $name,
          $username,
          $password,
          $avatar
        );
      };

      return $userEntity;  
    }

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function store(Entity $entity)
    {
        //@TODO some erro checking/ exception throwing
        $entity = $this->hashPassword($entity);
        $this->_em->persist($entity);
        $this->_em->flush();
        //@TODO try catch check if email is unique value then return a formatted response at moment returns geenri sql error
        return $entity;
    }

    /**
     * @param Entity $entity
     * @return Entity|null|object
     */
    public function update(Entity $entity)
    {
        $entity = $this->hashPassword($entity);
        $this->_em->merge($entity);
        $this->_em->flush();
        //@TODO try catch check if email is unique value then return a formatted response at moment returns geenri sql error
        return $entity;
    }

    /**
     * @param Entity $entity
     * @return Entity
     */
    public function hashPassword(Entity $entity)
    {
        $unHashedPass = $entity->getPassword();
        $entity = $entity->setPassword(app()->make('hash')->make($unHashedPass));
        return $entity;
    }

    public function findUserFromEmail($email)
    {
      $userEntity = $this->findOneBy(['email' => $email]);
      return $userEntity;
    }

    public function FindUserFromUserName($username)
    {
      $userEntity = $this->findOneBy(['username' => $username]);
      return $userEntity;
    }
}
