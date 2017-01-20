<?php

namespace ApiArchitect\Auth\Repositories;

use Jkirkby91\LumenDoctrineComponent\Repositories\LumenDoctrineEntityRepository;

/**
 * Class RoleRepository
 *
 * @package ApiArchitect\Auth\Repositories
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class RoleRepository extends LumenDoctrineEntityRepository implements \Jkirkby91\Boilers\RepositoryBoiler\CrudRepositoryContract
{
    use \Jkirkby91\DoctrineRepositories\CrudRepositoryTrait;
}