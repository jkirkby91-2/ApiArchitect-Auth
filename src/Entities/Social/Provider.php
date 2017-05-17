<?php

namespace ApiArchitect\Auth\Entities\Social;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Jkirkby91\DoctrineSchemas\Entities\Thing;

/**
 * Class Provider
 *
 * @package ApiArchitect\Auth\Entities\Social
 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\SocialAccountRepository")
 * @ORM\Table(name="provider", indexes={@ORM\Index(name="search_idx", columns={"name"})})
 * @Gedmo\Loggable
 * @ORM\HasLifecycleCallbacks
 *
 * @package ApiArchitect\Auth\Entities\Social
 * @author James Kirkby <me@jameskirkby.com>
 */
class Provider extends Thing 
{
    /**
     * Provider constructor.
     */
    public function __construct($provider)
    {
        $this->nodeType = 'Provider';
        $this->name = $provider;
    }
}
