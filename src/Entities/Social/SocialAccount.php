<?php

namespace ApiArchitect\Auth\Entities\Social;

use Doctrine\ORM\Mapping as ORM;
use ApiArchitect\Auth\Entities\User;
use Gedmo\Mapping\Annotation as Gedmo;
use Jkirkby91\DoctrineSchemas\Entities\Thing;
use ApiArchitect\Auth\Entities\Social\Provider;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Class Provider
 *
 * @package ApiArchitect\Auth\Entities\Social
 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\SocialAccountRepository")
 * @ORM\Table(name="social_account", indexes={@ORM\Index(name="search_idx", columns={"name"})})
 * @Gedmo\Loggable
 * @ORM\HasLifecycleCallbacks
 *
 * @package ApiArchitect\Auth\Entities\Social
 * @author James Kirkby <me@jameskirkby.com>
 */
class SocialAccount extends Thing 
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
   * Provider constructor.
   */
  public function __construct(Provider $provider, $oauthUser)
  {
    $this->name = md5($provider->getId().$oauthUser->getId());
    $this->nodeType = 'SocialAccount';
    $this->provider = $provider;
    $this->providerUid = $oauthUser->getId();
    $this->user = new ArrayCollection();
  }

  /**
   * Gets the value of provider.
   *
   * @return mixed
   */
  public function getProvider()
  {
      return $this->provider;
  }

  /**
   * Sets the value of provider.
   *
   * @param mixed $provider the provider
   *
   * @return self
   */
  public function setProvider($provider)
  {
      $this->provider = $provider;

      return $this;
  }

  /**
   * Gets the value of providerId.
   *
   * @return mixed
   */
  public function getProviderId()
  {
      return $this->providerUid;
  }

  /**
   * Sets the value of providerId.
   *
   * @param mixed $providerId the provider id
   *
   * @return self
   */
  public function setProviderId($providerId)
  {
      $this->providerUid = $providerId;

      return $this;
  }

  /**
   * @param User $user
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
   * @param User $user
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
