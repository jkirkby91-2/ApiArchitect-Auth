<?php

namespace ApiArchitect\Auth\Repositories;

use ApiArchitect\Compass\Repositories\AbstractRepository;
use Jkirkby91\DoctrineRepositories\ResourceRepositoryTrait;
use Jkirkby91\Boilers\NodeEntityBoiler\EntityContract AS Entity;
use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract;

/**
 * Class UserRepository
 *
 * @package  ApiArchitect\Compass\Repositories\UserRepository
 * @author James Kirkby <me@jameskirkby.com>
 */
class UserRepository extends AbstractRepository implements ResourceRepositoryContract
{
    use ResourceRepositoryTrait;

    public function findOrCreateUser($target)
    {
      $user = $this->findBy(['email' => $target['email']]);

      if(empty($user)){
        dd($user);      
      } else {
        dd('lol');
      }
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
