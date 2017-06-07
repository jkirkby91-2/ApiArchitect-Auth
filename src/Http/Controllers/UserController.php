<?php

namespace ApiArchitect\Auth\Http\Controllers;

use Tymon\JWTAuth\JWTAuth;
use ApiArchitect\Auth\Entities\User;
use Psr\Http\Message\ServerRequestInterface;
use Jkirkby91\Boilers\RestServerBoiler\Exceptions;
use Spatie\Fractal\ArraySerializer AS ArraySerialization;
use ApiArchitect\Compass\Http\Controllers\RestApi;
use Jkirkby91\Boilers\RestServerBoiler\TransformerContract AS ObjectTransformer;
use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;

/**
 * Class USerController
 *
 * @package app\Http\Controllers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
final class UserController extends RestApi
{

  /**
   * @var $auth
   */
  protected $auth;

  public function __construct(ResourceRepository $repository, ObjectTransformer $objectTransformer, JWTAuth $auth)
  {
      $this->auth = $auth;    
      parent::__construct($repository,$objectTransformer);
  }

  public function index(ServerRequestInterface $request) {

    $resource = $this->item($this->user)
          ->transformWith($this->transformer)
          ->serializeWith(new ArraySerialization())
          ->toArray();

    return $this->showResponse($resource);
  }

  /**
   * @param ServerRequestInterface $request
   * @return mixed
   */
  public function register(ServerRequestInterface $request)
  {
    return $this->store($request);
  }

  /**
   * @param ServerRequestInterface $request
   * @return mixed
   */
  public function store(ServerRequestInterface $request)
  {

    $userRegDetails = $request->getParsedBody();

    $userEntity = $this->repository->findOrCreateUser(
      $userRegDetails['email'],
      $userRegDetails['name'],
      $userRegDetails['username'],
      $userRegDetails['role'],
      $userRegDetails['password']
    );

    $token = $this->auth->fromUser($userEntity);

    $resource = $this->item($userEntity)
          ->transformWith($this->transformer)
          ->addMeta(['token' => $token])
          ->serializeWith(new ArraySerialization());

    return $this->createdResponse($resource);
  }

  /**
   * @param ServerRequestInterface $request
   * @param $id
   * @return mixed
   */
  public function update(ServerRequestInterface $request, $id) 
  {
    $userProfileDetails = $request->getParsedBody();

    try {
      if (!$data = $this->repository->findUserFromEmail($this->user->getEmail())) {
        throw new Exceptions\NotFoundHttpException();
      }
    } catch (Exceptions\NotFoundHttpException $exception) {
      $this->notFoundResponse();
    }

    // if (isset($userProfileDetails['roles'])) {
    //   $data = $data->addRoles($userProfileDetails['roles']);
    // }

    if (isset($userProfileDetails['username'])) {
      $data = $data->setUserName($userProfileDetails['username']);
    }

    if (isset($userProfileDetails['username'])) {
      $data = $data->setEmail($userProfileDetails['email']);
    }

    //@TODO Create a new route for password resets that does some validation middleware
    if (isset($userProfileDetails['password'])) {

      try {
        if ($userProfileDetails['password'] !== $userProfileDetails['password_confirmation']) {
          throw new Exceptions\UnprocessableEntityException('Passwords do not match');
        }
      } catch (Exceptions\UnprocessableEntityException $exception) {
        $this->clientErrorResponse($exception->getMessage());
      }

      $data = $data->setPassword($userProfileDetails['password']);
    }

    if (isset($userProfileDetails['permissions'])) {
      $data = $data->setPermissions($userProfileDetails['permissions']);
    }

    $this->repository->update($data);

    $resource = $this->item($data)
          ->transformWith($this->transformer)
          ->serializeWith(new ArraySerialization())
          ->toArray();

    return $this->createdResponse($resource);
  }

  /**
   * @TODO check email is unique
   */
  public function checkUniqueEmail(ServerRequestInterface $request)
  {
    $emailTarget = $request->getParsedBody();

    if (!in_array('email', $emailTarget))
    {
      throw new Exceptions\UnprocessableEntityException('no email defined');
    }

    $userEntity = $this->repository->findUserFromEmail($emailTarget['email']);

    if (is_null($userEntity)) {
      return true;
    } else {
      return false;
    }
  }

  /**
   * @TODO chcek username is unique
   */
  public function checkUniqueUserName(ServerRequestInterface $request) 
  {
    $userNameTarget = $request->getParsedBody();

    if (!in_array('username', $userNameTarget))
    {
      throw new Exceptions\UnprocessableEntityException('no username defined');
    }

    $userEntity = $this->repository->FindUserFromUserName($userNameTarget['username']);

    if (is_null($userEntity)) {
      return true;
    } else {
      return false;
    }
  }
}