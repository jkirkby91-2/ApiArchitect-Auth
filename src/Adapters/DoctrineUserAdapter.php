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
	 * byCredentials()
	 * @param array $credentials
	 *
	 * @return \ApiArchitect\Auth\Entities\User|bool|mixed
	 * @throws \Doctrine\ORM\EntityNotFoundException
	 */
    public function byCredentials(array $credentials)
    {
        //try get a user
        $authTarget = $this->ifFound($this->doctrineUserProvider->retrieveByCredentials($credentials));

        //validate found user
        if ($this->doctrineUserProvider->validateCredentials($authTarget,$credentials) === true){
            return $authTarget;
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
		return $this->ifFound($this->doctrineUserProvider->retrieveById($id));
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
	 * ifFound()
	 * @param \ApiArchitect\Auth\Entities\User $user
	 *
	 * @return \ApiArchitect\Auth\Entities\User
	 * @throws \Doctrine\ORM\EntityNotFoundException
	 */
    private function ifFound(\ApiArchitect\Auth\Entities\User $user)
    {
        if (is_null($user) || !is_a($user, 'ApiArchitect\Auth\Entities\User')) {
            throw new EntityNotFoundException;
        } else {
            if ($user->getEnabled() != false) {
            	$this->user = $user;
                return $user;
            } else {
                throw new UnauthorizedHttpException('User Account Has Been Banned');
            }
        }
    }
}