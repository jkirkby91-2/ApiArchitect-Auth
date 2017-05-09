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

    public function findOrCreateUser(User $target)
    {
      $userEntity = $this->findOneBy(['email' => $target->getEmail()]);

      if(!empty($userEntity)){
        return $userEntity;
      }

      $userEntity = $this->store($target);

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

}
