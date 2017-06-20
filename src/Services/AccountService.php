<?php

namespace ApiArchitect\Auth\Services;

use ApiArchitect\Auth\Entities\User;
use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;

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
  public function __construct(ResourceRepository $repository)
  {
    $this->repository = $repository;
  }

	/**
	 * createNewAccount()
	 *
	 * @param      $email
	 * @param      $name
	 * @param      $username
	 * @param null $role
	 * @param null $password
	 * @param null $avatar
	 *
	 * @return $this|\ApiArchitect\Auth\Entities\User|mixed
	 */
  public function createNewAccount($email, $name, $username, $role=null, $password=null, $avatar=null)
  {
    $userEntity = new User($email,$name,$username);
   
    if($password === null)
    {
      $userEntity->setOTP(1);
      $userEntity->setPassword(md5(microtime(true).$email.$name));
    } else {
      $userEntity = $userEntity->setPassword(app()->make('hash')->make($password));
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
      throw new \Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnprocessableEntityException('target role not found');
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