<?php

namespace ApiArchitect\Auth\Repositories;

use Jkirkby91\LumenDoctrineComponent\Repositories\LumenDoctrineEntityRepository;

/**
 * Class ProviderRepository
 *
 * @package ApiArchitect\Auth\Repositories
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class ProviderRepository extends LumenDoctrineEntityRepository implements \Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract
{
    use \Jkirkby91\DoctrineRepositories\ResourceRepositoryTrait;
}