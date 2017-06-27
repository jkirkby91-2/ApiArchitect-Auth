<?php

namespace ApiArchitect\Auth\Adapters;

use Illuminate\Hashing\BcryptHasher;
use Doctrine\ORM\EntityNotFoundException;
use Tymon\JWTAuth\Contracts\Providers\Auth;
use LaravelDoctrine\ORM\Auth\DoctrineUserProvider;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class DoctrineUserAdapter
 *
 * Doctrine authentication driver for JWT Auth
 *
 * @package ApiArchitect\Auth\Adapters
 * @author  James Kirkby <jkirkby@protonmail.ch>
 */
class DoctrineUserAdapter implements Auth
{

	/**
	 * @var
	 */
    protected $auth;

	/**
	 * @var
	 */
    protected $user;

	/**
	 * @var \LaravelDoctrine\ORM\Auth\DoctrineUserProvider
	 */
    protected $doctrineUserProvider;

    /**
     * DoctrineUserAdapter constructor.
     *
     * @param DoctrineUserProvider $doctrineUserProvider
     */
    public function __construct(DoctrineUserProvider $doctrineUserProvider)
    {
        $this->doctrineUserProvider = $doctrineUserProvider;
    }

    /**
     * @param array $credentials
     * @return bool|\Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function byCredentials(array $credentials)
    {
        //try get a user
        $authTarget = $this->ifFound($this->doctrineUserProvider->retrieveByCredentials($credentials));

        //validate found user
        if($this->doctrineUserProvider->validateCredentials($authTarget,$credentials) === true){
            return $this->user = $this->ifFound($authTarget);
        } else {
            return false;
        }
    }

	/**
	 * byId()
	 * @param mixed $id
	 *
	 * @return mixed
	 */
    public function byId($id)
    {
		$this->user = $this->ifFound($this->doctrineUserProvider->retrieveById($id));

		return $this->user;
	}

	/**
	 * user()
	 * @return mixed
	 */
    public function user()
    {
        return $this->user;
    }

    /**
     * Check the returned object has a user or throw exception
     *
	 * @TODO type hint user object
     * @param $object
     * @return mixed
     * @throws EntityNotFoundException
     */
    private function ifFound($object)
    {
        if (is_null($object) || !is_a($object, 'ApiArchitect\Auth\Entities\User')) {
            throw new EntityNotFoundException;
        } else {
            if ($object->getEnabled() != false) {
                return $object;
            } else {
                throw new UnauthorizedHttpException('User Account Has Been Banned');
            }
        }
    }
}