<?php

namespace ApiArchitect\Auth\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation AS Gedmo;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use Jkirkby91\LumenDoctrineComponent\Entities\LumenDoctrineEntity;

/**
 * Class Role
 *
 * @package app\Entities
 * @author James Kirkby <jkirkby91@gmail.com>
 *
 * @Gedmo\Loggable
 * @ORM\Entity
 * @ORM\Table(name="role", indexes={@ORM\Index(name="name_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\RoleRepository")
 */
class Role extends LumenDoctrineEntity implements RoleContract
{

    /**
     * Role constructor.
     */
    public function __construct($name)
    {
        $this->nodeType = 'Role';
        $this->name = $name;
    }

    /**
     * @Gedmo\Versioned
     * @Gedmo\Blameable(on="create")
     * @Gedmo\IpTraceable(on="create")
     * @ORM\Column(type="string", length=45, nullable=true)
     */
    protected $name;

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
}