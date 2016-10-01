<?php

namespace ApiArchitect\Auth\Entities;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class PasswordResets
 *
 * @package ApiArchitect\Auth\Entities
 * @author James Kirkby <jkirkby91@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="password_resets", indexes={@ORM\Index(name="token_idx", columns={"token"})})
 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\PasswordResetsRepository")
 * @ORM\HasLifeCycleCallBacks
 * @TODO hit the post flush lifecycle event and push the object to email queue
 */
class PasswordResets extends \Jkirkby91\LumenDoctrineComponent\Entities\LumenDoctrineEntity
{

    /**
     * @var
     * @ORM\OneToOne(targetEntity="\ApiArchitect\Compass\Entities\User")
     */
    protected $user;

    /**
     * @var
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $token;

    /**
     * @var
     * @ORM\Column(type="boolean", length=45, nullable=false)
     */
    protected $used;

    /**
     * PasswordResets constructor.
     *
     * @param $user
     * @param $token
     */
    public function __construct($user, $token, $used)
    {
        $this->user     = $user;
        $this->token    = $token;
        $this->used     = $used;
        $this->nodeType = 'PasswordReset';
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     * @return PasswordResets
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     * @return PasswordResets
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUsed()
    {
        return $this->used;
    }

    /**
     * @param mixed $used
     * @return PasswordResets
     */
    public function setUsed($used)
    {
        $this->used = $used;
        return $this;
    }
}