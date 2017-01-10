<?php

namespace ApiArchitect\Auth\Entities;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Class Role
 *
 * @package app\Entities
 * @author James Kirkby <jkirkby91@gmail.com>
 *
 * @ORM\Entity
 * @ORM\Table(name="role", indexes={@ORM\Index(name="name_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\RoleRepository")
 */
class Role extends \Jkirkby91\LumenDoctrineComponent\Entities\LumenDoctrineEntity implements \LaravelDoctrine\ACL\Contracts\Role
{

    /**
     * Role constructor.
     */
    public function __construct()
    {
        $this->nodeType = 'Role';
    }

    /**
     * @Gedmo\Versioned
     * @Gedmo\Blameable(on="create")
     * @Gedmo\IpTraceable(on="create")
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $name;

    /**
     * @Gedmo\Versioned
     * @Gedmo\Blameable(on="create")
     * @Gedmo\IpTraceable(on="create")
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $permission;

    /**
     * @return mixed
     */
    public function getPermissions()
    {
        return $this->permission;
    }

    /**
     * @param string $permission
     * @return $this
     */
    public function hasPermissionTo($permission)
    {
        $this->permission = $permission;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPermission()
    {
        return $this->permission;
    }

    /**
     * @param mixed $permission
     * @return Role
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
}