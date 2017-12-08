<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Entities {

		use Doctrine\{
			ORM\Mapping as ORM, ORM\Event\LifecycleEventArgs
		};

		use Gedmo\{
			Mapping\Annotation as Gedmo
		};

		use LaravelDoctrine\{
			ACL\Contracts\Role as RoleContract
		};

		use ApiArchitect\{
			Compass\Entities\AbstractResourceEntity
		};

		/**
		 * Class Role
		 *
		 * @package app\Entities
		 * @author James Kirkby <jkirkby91@gmail.com>
		 *
		 * @Gedmo\Loggable
		 * @ORM\HasLifeCycleCallbacks
		 * @ORM\Entity
		 * @ORM\Table(name="role", indexes={@ORM\Index(name="role_name_idx", columns={"name"})})
		 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\RoleRepository")
		 */
		class Role extends AbstractResourceEntity implements RoleContract
		{

			/**
			 * Role constructor.
			 *
			 * @param $name
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
			 * setName()
			 * @param string $name
			 *
			 * @return $this|\Jkirkby91\Boilers\SchemaBoilers\SchemaContract
			 */
			public function setName(string $name)
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
	}