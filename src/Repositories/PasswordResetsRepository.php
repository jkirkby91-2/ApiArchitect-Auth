<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Repositories {

		use Jkirkby91\{
			DoctrineRepositories\CrudRepositoryTrait,
			LumenDoctrineComponent\Repositories\LumenDoctrineEntityRepository
		};

		/**
		 * Class PasswordResetsRepository
		 *
		 * @package ApiArchitect\Auth\Repositories
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class PasswordResetsRepository extends LumenDoctrineEntityRepository implements \Jkirkby91\Boilers\RepositoryBoiler\CrudRepositoryContract
		{
			use CrudRepositoryTrait;
		}
	}