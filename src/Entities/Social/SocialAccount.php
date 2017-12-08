<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Entities\Social {

		use Doctrine\{
			ORM\Mapping as ORM, Common\Collections\ArrayCollection
		};

		use ApiArchitect\{
			Auth\Entities\User,
			Compass\Entities\AbstractResourceEntity,
			Auth\Entities\Social\Provider
		};

		use Gedmo\{
			Mapping\Annotation as Gedmo
		};

		/**
		 * Class SocialAccount
		 *
		 * @package ApiArchitect\Auth\Entities\Social
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 *
		 * @package ApiArchitect\Auth\Entities\Social
		 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\SocialAccountRepository")
		 * @ORM\Table(name="social_account", indexes={@ORM\Index(name="social_account_search_idx", columns={"name"})})
		 * @Gedmo\Loggable
		 * @ORM\HasLifecycleCallbacks
		 */
		class SocialAccount extends AbstractResourceEntity
		{

			/**
			 * @var ArrayCollection
			 * @ORM\ManyToOne(targetEntity="ApiArchitect\Auth\Entities\Social\Provider", cascade={"all"}, fetch="EXTRA_LAZY")
			 */
			protected $provider;

			/**
			 * @ORM\Column(type="string", unique=false, nullable=true)
			 */
			protected $providerUid;

			/**
			 * @var \Doctrine\Common\Collections\Collection|User[]
			 *
			 * @ORM\ManyToMany(targetEntity="ApiArchitect\Auth\Entities\User", mappedBy="socialAccounts", cascade={"all"}, fetch="EXTRA_LAZY")
			 */
			protected $user;

			/**
			 * SocialAccount constructor.
			 *
			 * @param \ApiArchitect\Auth\Entities\Social\Provider $provider
			 * @param \ApiArchitect\Auth\Entities\User            $oauthUser
			 */
			public function __Construct(Provider $provider,User $oauthUser)
			{
				parent::__Construct(md5($provider->getId().$oauthUser->getId()));
				$this->provider = $provider;
				$this->nodeType = 'SocialAccount';
				$this->user = new ArrayCollection();
				$this->providerUid = $oauthUser->getId();
			}

			/**
			 * getProvider()
			 * @return \ApiArchitect\Auth\Entities\Social\Provider
			 */
			public function getProvider() : Provider
			{
				return $this->provider;
			}

			/**
			 * setProvider()
			 * @param \ApiArchitect\Auth\Entities\Social\Provider $provider
			 *
			 * @return \ApiArchitect\Auth\Entities\Social\SocialAccount
			 */
			public function setProvider(Provider $provider) : SocialAccount
			{
				$this->provider = $provider;

				return $this;
			}

			/**
			 * getProviderId()
			 * @return int
			 */
			public function getProviderId() : int
			{
				return $this->providerUid;
			}

			/**
			 * setProviderId()
			 * @param int $providerId
			 *
			 * @return \ApiArchitect\Auth\Entities\Social\SocialAccount
			 */
			public function setProviderId(int $providerId) : SocialAccount
			{
				$this->providerUid = $providerId;

				return $this;
			}

			/**
			 * addUser()
			 * @param \ApiArchitect\Auth\Entities\User $user
			 */
			public function addUser(User $user)
			{
				if ($this->user->contains($user)) {
					return;
				}
				$this->user->add($user);
				$user->addSocialAccount($this);
			}

			/**
			 * removeUser()
			 * @param \ApiArchitect\Auth\Entities\User $user
			 */
			public function removeUser(User $user)
			{
				if (!$this->user->contains($user)) {
					return;
				}
				$this->user->removeElement($user);
				$user->removeSocialAccount($this);
			}
		}
	}
