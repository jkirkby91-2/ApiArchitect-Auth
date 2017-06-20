<?php

namespace ApiArchitect\Auth\Entities;

use Doctrine\ORM\Mapping AS ORM;
use Gedmo\Mapping\Annotation AS Gedmo;
use Doctrine\ORM\Event\LifecycleEventArgs;
use LaravelDoctrine\ACL\Contracts\Role as RoleContract;
use ApiArchitect\Compass\Entities\AbstractResourceEntity;

/**
 * Class Role
 *
 * @package app\Entities
 * @author James Kirkby <jkirkby91@gmail.com>
 *
 * @Gedmo\Loggable
 * @ORM\HasLifeCycleCallbacks
 * @ORM\Entity
 * @ORM\Table(name="role", indexes={@ORM\Index(name="name_idx", columns={"name"})})
 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\RoleRepository")
 */
class Role extends AbstractResourceEntity implements RoleContract
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