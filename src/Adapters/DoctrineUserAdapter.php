<?php

namespace ApiArchitect\Auth\Adapters;

use ApiArchitect\Compass\Entities\User;
use Illuminate\Hashing\BcryptHasher;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;

/**
 * Class DoctrineUserAdapter
 *
 * Authentication driver for JWT Auth
 *
 * @package app\Providers\User
 * @author James Kirkby <me@jameskirkby.com>
 */
class DoctrineUserAdapter implements Auth
{

    /**
     * @var DoctrineUserProvider
     */
    protected $doctrineUserAdapter;

    /**
     * @var
     */
    protected $auth;

    /**
     * DoctrineUserAdapter constructor.
     *
     * @param DoctrineUserProvider $doctrineUserProvider
     */
    public function __construct(DoctrineUserProvider $doctrineUserProvider)
    {
        $this->doctrineUserAdapter = $doctrineUserProvider;
    }

    /**
     * @param array $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function byCredentials(array $credentials)
    {
        //try get the auth target user
        $authTarget = $this->doctrineUserAdapter->retrieveByCredentials($credentials);

        //check we actually have a user returned
        $this->ifFound($authTarget);

        //validate found user
        if($this->doctrineUserAdapter->validateCredentials($authTarget,$credentials) === true){
            $this->auth = $authTarget;
            return $this->auth;
        } else {
            throw new \Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnauthorizedHttpException();
        }
    }

    /**
     * @param mixed $id
     * @return mixed
     */
    public function byId($id)
    {

        return $this->ifFound($this->doctrineUserAdapter->retrieveById($id));

    }

    /**
     * @return mixed
     */
    public function user()
    {
        return $this->auth;
    }

    /**
     * Check the returned object has a user or throw exception
     *
     * @param $object
     * @return mixed
     */
    private function ifFound($object)
    {
        if(is_null($object))
        {
            throw new \Jkirkby91\Boilers\RestServerBoiler\Exceptions\NotFoundHttpException('User Not Found');
        } else {
            return $object;
        }
    }

}
