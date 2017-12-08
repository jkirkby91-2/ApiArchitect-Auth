<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Repositories {

		use Jkirkby91\{
			DoctrineRepositories\ResourceRepositoryTrait,
			Boilers\RepositoryBoiler\ResourceRepositoryContract,
			LumenDoctrineComponent\Repositories\LumenDoctrineEntityRepository
		};

		/**
		 * Class RoleRepository
		 *
		 * @package ApiArchitect\Auth\Repositories
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class RoleRepository extends LumenDoctrineEntityRepository implements ResourceRepositoryContract
		{
			use ResourceRepositoryTrait;
		}
	}
