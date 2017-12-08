<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Entities {

		use Doctrine\{
			Common\Collections\Collection, ORM\Mapping as ORM, Common\Collections\ArrayCollection
		};

		use ApiArchitect\{
			Auth\Entities\Role,
			Auth\Entities\Social\SocialAccount,
			Compass\Entities\AbstractResourceEntity
		};

		use Gedmo\{
			Mapping\Annotation as Gedmo
		};

		use Tymon\{
			JWTAuth\Contracts\JWTSubject
		};

		use LaravelDoctrine\{
			ACL\Mappings as ACL, ACL\Permissions\Permission, ACL\Roles\HasRoles as HasRolesTrait, ACL\Contracts\HasRoles as HasRolesContract, ORM\Auth\Authenticatable as AuthenticatableTrait, ACL\Contracts\HasPermissions as HasPermissionContract, ACL\Permissions\HasPermissions as HasPermissionsTrait
		};

		use Laravel\{
			Socialite\Contracts\User as SocialUserContract
		};

		use Illuminate\{
			Auth\Passwords\CanResetPassword as CanResetPasswordTrait,
			Contracts\Auth\Authenticatable as AuthenticatableContract,
			Contracts\Auth\CanResetPassword as CanResetPasswordContract
		};

		/**
		 * Class User
		 *
		 * @package ApiArchitect\Auth\Entities
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 *
		 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\UserRepository")
		 * @ORM\Table(name="users", indexes={@ORM\Index(name="users_search_idx", columns={"email"})})
		 * @Gedmo\Loggable
		 * @ORM\HasLifecycleCallbacks
		 */
		class User extends AbstractResourceEntity implements AuthenticatableContract, JWTSubject, CanResetPasswordContract, HasRolesContract, HasPermissionContract, SocialUserContract
		{

			use HasRolesTrait, HasPermissionsTrait, AuthenticatableTrait, CanResetPasswordTrait;

			/**
			 * @ORM\Column(type="string", nullable=false)
			 */
			protected $enabled;

			/**
			 * @var ArrayCollection
			 * @ORM\ManyToMany(targetEntity="\ApiArchitect\Auth\Entities\Role", cascade={"all"}, fetch="EXTRA_LAZY")
			 * @ORM\JoinTable(name="user_roles",
			 *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
			 *      inverseJoinColumns={@ORM\JoinColumn(name="role_id", referencedColumnName="id", unique=false)})
			 */
			protected $roles;

			/**
			 * @ORM\Column(type="string", nullable=true)
			 */
			protected $firstName;

			/**
			 * @ORM\Column(type="string", nullable=true)
			 */
			protected $lastName;

			/**
			 * @ORM\Column(type="string", nullable=true)
			 */
			protected $username;

			/**
			 * @var
			 * @ORM\Column(type="string",unique=true, nullable=false)
			 */
			protected $email;

			/**
			 * @ORM\Column(type="string", nullable=false)
			 */
			protected $password;

			/**
			 * @ACL\HasPermissions
			 */
			public $permissions;

			/**
			 * @ORM\Column(name="remember_token", type="string", nullable=true)
			 */
			protected $rememberToken;

			/**
			 * @ORM\Column(type="string", nullable=true)
			 */
			protected $avatar;

			/**
			 * @ORM\Column(type="integer", unique=false, nullable=true)
			 */
			protected $OTP;

			/**
			 * @var \Doctrine\Common\Collections\Collection|UserGroup[]
			 *
			 * @ORM\ManyToMany(targetEntity="\ApiArchitect\Auth\Entities\Social\SocialAccount", inversedBy="user", fetch="EXTRA_LAZY")
			 * @ORM\JoinTable(
			 *  name="user_socialaccount",
			 *  joinColumns={
			 *      @ORM\JoinColumn(name="user_id", referencedColumnName="id")
			 *  },
			 *  inverseJoinColumns={
			 *      @ORM\JoinColumn(name="user_social_account_id", referencedColumnName="id")
			 *  }
			 * )
			 */
			protected $socialAccounts;

			/**
			 * User constructor.
			 *
			 * @param string $email
			 * @param string $username
			 * @param string $firstName
			 * @param string $lastName
			 */
			public function __Construct(string $email, string $username, string $firstName, string $lastName)
			{
				parent::__construct($firstName. ' ' .$lastName);

				$this->setFirstName($firstName);
				$this->setLastName($lastName);
				$this->setEmail($email);
				$this->setEnabled(TRUE);
				$this->setNodeType('User');
				$this->setUserName($username);
				$this->roles = new ArrayCollection();
				$this->socialAccounts = new ArrayCollection();
			}

			/**
			 * getEnabled()
			 * @return bool
			 */
			public function getEnabled() : bool
			{
				return $this->enabled;
			}

			/**
			 * setEnabled()
			 * @param bool $enabled
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setEnabled(bool $enabled) : User
			{
				if (is_bool($enabled)) {
					$this->enabled = $enabled;

					return $this;
				}
			}

			/**
			 * getPassword()
			 * @return string
			 */
			public function getPassword() : string
			{
				return $this->password;
			}

			/**
			 * setPassword()
			 * @param string $password
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setPassword(string $password) : User
			{
				$this->password = $password;

				return $this;
			}

			/**
			 * getUsername()
			 * @return string
			 */
			public function getUsername() : string
			{
				return $this->username;
			}

			/**
			 * setUsername()
			 * @param string $username
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setUsername(string $username) : User
			{
				$this->username = $username;

				return $this;
			}

			/**
			 * getEmail()
			 * @return string
			 */
			public function getEmail() : string
			{
				return $this->email;
			}

			/**
			 * setEmail()
			 * @param string $email
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setEmail(string $email) : User
			{
				$this->email = $email;

				return $this;
			}

			/**
			 * getRememberToken()
			 * @return string
			 */
			public function getRememberToken() : string
			{
				return $this->rememberToken;
			}

			/**
			 * setRememberToken()
			 * @param string $value
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setRememberToken($value)
			{
				$this->rememberToken = $value;

				return $this;
			}

			/**
			 * Get the column name for the "remember me" token.
			 *
			 * @return string
			 */
			public function getRememberTokenName() : string
			{
				return "rememberToken";
			}

			/**
			 * getRoles()
			 * @return \Doctrine\Common\Collections\Collection
			 */
			public function getRoles() : Collection
			{
				return $this->roles;
			}

			/**
			 * addRoles()
			 * @param \ApiArchitect\Auth\Entities\Role $role
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function addRoles(Role $role) : User
			{
				if (!$this->roles->contains($role)) {
					$this->roles->add($role);
				}

				return $this;
			}

			/**
			 * getPermissions()
			 * @return \LaravelDoctrine\ACL\Permissions\Permission
			 */
			public function getPermissions() : Permission
			{
				return $this->permissions;
			}

			/**
			 * setPermissions()
			 * @param \LaravelDoctrine\ACL\Permissions\Permission $permissions
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setPermissions(Permission $permissions) : User
			{
				$this->permissions = $permissions;

				return $this;
			}

			/**
			 * getAvatar()
			 * @return string|null
			 */
			public function getAvatar()
			{
				return $this->avatar;
			}

			/**
			 * setAvatar()
			 * @param string $avatar
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setAvatar(string $avatar) : User
			{
				$this->avatar = $avatar;

				return $this;
			}

			/**
			 * getNickname()
			 * @return string|null
			 */
			public function getNickname()
			{
				return $this->username;
			}

			/**
			 * setNickname()
			 * @param string $nickname
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setNickname(string $nickname): User
			{
				$this->username = $nickname;

				return $this;
			}

			/**
			 * getOTP()
			 * @return string
			 */
			public function getOTP() : string
			{
				return $this->OTP;
			}

			/**
			 * setOTP()
			 * @param string $OTP
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setOTP(string $OTP) : User
			{
				$this->OTP = $OTP;

				return $this;
			}

			/**
			 * addSocialAccount()
			 *
			 * @param \ApiArchitect\Auth\Entities\Social\SocialAccount $socialAccount
			 */
			public function addSocialAccount(SocialAccount $socialAccount)
			{
				if ($this->socialAccounts->contains($socialAccount)) {
					return;
				}
				$this->socialAccounts->add($socialAccount);
				$socialAccount->addUser($this);
			}

			/**
			 * removeSocialAccount()
			 *
			 * @param \ApiArchitect\Auth\Entities\Social\SocialAccount $socialAccount
			 */
			public function removeSocialAccount(SocialAccount $socialAccount)
			{
				if (!$this->socialAccounts->contains($socialAccount)) {
					return;
				}
				$this->socialAccounts->removeElement($socialAccount);
				$socialAccount->removeUser($this);
			}

			/**
			 * getJWTIdentifier()
			 *
			 * Get the identifier that will be stored in the subject claim of the JWT
			 *
			 * @return int
			 */
			public function getJWTIdentifier() : int
			{
				return $this->getId();
			}

			/**
			 * getJWTCustomClaims()
			 *
			 * Return a key value array, containing any custom claims to be added to the JWT
			 *
			 * @return array
			 */
			public function getJWTCustomClaims() : array
			{
				return [];
			}

			/**
			 * getFirstName()
			 * @return string
			 */
			public function getFirstName() : string
			{
				return $this->firstName;
			}

			/**
			 * setFirstName()
			 * @param string $firstName
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setFirstName(string $firstName) : User
			{
				$this->firstName = $firstName;

				return $this;
			}

			/**
			 * getLastName()
			 * @return string
			 */
			public function getLastName() : string
			{
				return $this->lastName;
			}

			/**
			 * setLastName()
			 * @param string $lastName
			 *
			 * @return \ApiArchitect\Auth\Entities\User
			 */
			public function setLastName(string $lastName) : User
			{
				$this->lastName = $lastName;

				return $this;
			}

		}
	}
