<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Entities\Social {

		use Doctrine\{
			ORM\Mapping as ORM
		};

		use Gedmo\{
			Mapping\Annotation as Gedmo
		};

		use ApiArchitect\{
			Compass\Entities\AbstractResourceEntity
		};

		/**
		 * Class Provider
		 *
		 * @package ApiArchitect\Auth\Entities\Social
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 *
		 * @ORM\Entity(repositoryClass="ApiArchitect\Auth\Repositories\SocialAccountRepository")
		 * @ORM\Table(name="provider", indexes={@ORM\Index(name="provider_search_idx", columns={"name"})})
		 * @Gedmo\Loggable
		 * @ORM\HasLifecycleCallbacks
		 */
		class Provider extends AbstractResourceEntity
		{

			/**
			 * Provider constructor.
			 *
			 * @param string $provider
			 */
			public function __Construct(string $provider)
			{
				parent::__Construct($provider);
				$this->nodeType = 'Provider';
			}
		}
	}
