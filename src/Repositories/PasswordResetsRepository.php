<?php

namespace ApiArchitect\Auth\Repositories;

use Jkirkby91\LumenDoctrineComponent\Repositories\LumenDoctrineEntityRepository;

/**
 * Class PasswordResetsRepository
 *
 * @package ApiArchitect\Auth\Repositories
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class PasswordResetsRepository extends LumenDoctrineEntityRepository implements \Jkirkby91\Boilers\RepositoryBoiler\CrudRepositoryContract
{
    use \Jkirkby91\DoctrineRepositories\CrudRepositoryTrait;
}