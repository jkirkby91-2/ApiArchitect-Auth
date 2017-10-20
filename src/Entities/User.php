<?php

	namespace ApiArchitect\Auth\Entities;

	use Doctrine\ORM\Mapping as ORM;
	use ApiArchitect\Auth\Entities\Role;
	use Gedmo\Mapping\Annotation as Gedmo;
	use Tymon\JWTAuth\Contracts\JWTSubject;
	use LaravelDoctrine\ACL\Mappings as ACL;
	use Doctrine\Common\Collections\ArrayCollection;
	use ApiArchitect\Auth\Entities\Social\SocialAccount;
	use LaravelDoctrine\ACL\Roles\HasRoles as HasRolesTrait;
	use Laravel\Socialite\Contracts\User AS SocialUserContract;
	use LaravelDoctrine\ACL\Contracts\HasRoles as HasRolesContract;
	use LaravelDoctrine\ORM\Auth\Authenticatable as AuthenticatableTrait;
	use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
	use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
	use LaravelDoctrine\ACL\Contracts\HasPermissions as HasPermissionContract;
	use LaravelDoctrine\ACL\Permissions\HasPermissions as HasPermissionsTrait;
	use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
	use ApiArchitect\Compass\Entities\AbstractResourceEntity;

	/**
	 * Class User
	 *
	 * @package ApiArchitect\Auth\Entities
	 * @author  James Kirkby <jkirkby@protonmail.ch>
	 *
	 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\UserRepository")
	 * @ORM\Table(name="users", indexes={@ORM\Index(name="search_idx", columns={"email"})})
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
		 * @param $email
		 * @param $name
		 * @param $username
		 */
		public function __construct($email, $name, $username)
		{
			$this->setName($name);
			$this->setEmail($email);
			$this->setEnabled(true);
			$this->setNodeType('User');
			$this->setUserName($username);
			$this->roles = new ArrayCollection();
			$this->socialAccounts = new ArrayCollection();
		}

		/**
		 * @return mixed
		 */
		public function getEnabled() {
			return $this->enabled;
		}

		/**
		 * @param $enabled
		 * @return $this
		 */
		public function setEnabled($enabled) {
			if(is_bool($enabled)){
				$this->enabled = $enabled;
				return $this;
			}
		}

		/**
		 * @return mixed
		 */
		public function getPassword() {
			return $this->password;
		}

		/**
		 * @param $password
		 * @return $this
		 */
		public function setPassword($password) {
			$this->password = $password;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getUsername() {
			return $this->username;
		}

		/**
		 * @param $username
		 * @return $this
		 */
		public function setUsername($username) {
			$this->username = $username;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getEmail() {
			return $this->email;
		}

		/**
		 * @param $username
		 * @return $this
		 */
		public function setEmail($email) {
			$this->email = $email;
			return $this;
		}

		/**
		 * Get the token value for the "remember me" session.
		 *
		 * @return string
		 */
		public function getRememberToken() {
			return $this->rememberToken;
		}

		/**
		 * @param string $value
		 * @return $this
		 */
		public function setRememberToken($value) {
			$this->rememberToken = $value;
			return $this;
		}
		/**
		 * Get the column name for the "remember me" token.
		 *
		 * @return string
		 */
		public function getRememberTokenName() {
			return "rememberToken";
		}

		/**
		 * @return \Doctrine\Common\Collections\ArrayCollection|\LaravelDoctrine\ACL\Contracts\Role[]
		 */
		public function getRoles() {
			return $this->roles;
		}

		/**
		 * @param OpeningHoursSpecification $openingHoursSpecification
		 * @return $this
		 */
		public function addRoles(Role $role) {
			if (!$this->roles->contains($role)) {
				$this->roles->add($role);
			}
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getPermissions() {
			return $this->permissions;
		}

		/**
		 * @param mixed $permissions
		 * @return User
		 */
		public function setPermissions($permissions) {
			$this->permissions = $permissions;
			return $this;
		}

		/**
		 * @return mixed
		 */
		public function getAvatar() {
			return $this->avatar;
		}

		/**
		 * @param mixed $avatar
		 * @return User
		 */
		public function setAvatar($avatar) {
			$this->avatar = $avatar;
			return $this;
		}

		/**
		 * Get the nickname / username for the user.
		 *
		 * @return string
		 */
		public function getNickname()
		{
			return $this->username;
		}

		/**
		 * Gets the value of OTP.
		 *
		 * @return mixed
		 */
		public function getOTP()
		{
			return $this->OTP;
		}

		/**
		 * Sets the value of OTP.
		 *
		 * @param mixed $OTP the
		 *
		 * @return self
		 */
		public function setOTP($OTP)
		{
			$this->OTP = $OTP;

			return $this;
		}

		/**
		 * addSocialAccount()
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
		 * Get the identifier that will be stored in the subject claim of the JWT
		 *
		 * @return mixed
		 */
		public function getJWTIdentifier() {
			return $this->getId();
		}
		/**
		 * Return a key value array, containing any custom claims to be added to the JWT
		 *
		 * @return array
		 */
		public function getJWTCustomClaims() {
			return [];
		}

	}
