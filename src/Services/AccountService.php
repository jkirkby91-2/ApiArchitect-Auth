<?php

namespace ApiArchitect\Auth\Services;

use ApiArchitect\Auth\Entities\User;
use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;

class AccountService 
{

  /**
   * @var ResourceRepository
   */
  protected $repository;

  /**
   * AccountService constructor.
   *
   * @param ResourceRepository $repository
   */
  public function __construct(ResourceRepository $repository)
  {
    $this->repository = $repository;
  }

  public function createNewAccount($email, $name, $username, $role=null, $password=null, $avatar=null)
  {
    $userEntity = new User($email,$name,$username);
   
    if($password === null)
    {
      $userEntity->setOTP(1);
      $userEntity->setPassword(md5(microtime(true).$email.$name));
    }

    if($role === null)
    {
      $role = 'user';
    }

    $roleEntity = app()
      ->make('em')
      ->getRepository('\ApiArchitect\Auth\Entities\Role')
      ->findOneBy(['name' => $role]);

    if (is_null($roleEntity)) {
      throw new Exceptions\UnprocessableEntityException('target role not found');
    }

    $userEntity->addRoles($roleEntity);

    if($avatar !== null)
    {
      $userEntity->setAvatar($avatar);
    }

    $userEntity = $this->repository->store($userEntity);

    return $userEntity;
  }
}