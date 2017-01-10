<?php

namespace ApiArchitect\Auth\Adapters;

use ApiArchitect\Compass\Entities\User;
use Doctrine\ORM\EntityNotFoundException;
use Illuminate\Hashing\BcryptHasher;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;

/**
 * Class DoctrineUserAdapter
 *
 * Authentication driver for JWT Auth
 *
 * @package app\Providers\User
 * @author James Kirkby <jkirkby91@gmail.com>
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
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function byCredentials(array $credentials)
    {
        //try get the auth target user
        $authTarget = $this->doctrineUserAdapter->retrieveByCredentials($credentials);

        //check we actually have a user returned
        $this->ifFound($this->doctrineUserAdapter->retrieveByCredentials($credentials));

        $x = $this->doctrineUserAdapter->validateCredentials($authTarget,$credentials);

        //validate found user
        if($this->doctrineUserAdapter->validateCredentials($authTarget,$credentials) === true){
            $this->auth = $authTarget;
            return $this->auth;
        } else {
            return false;
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
     * @throws EntityNotFoundException
     */
    private function ifFound($object)
    {
        if(is_null($object))
        {
            throw new EntityNotFoundException();
        } else {
            return $object;
        }
    }

}
