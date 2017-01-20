<?php

namespace ApiArchitect\Auth\Console\Commands;

use Illuminate\Console\Command;
use ApiArchitect\Auth\Entities\Role;

class CreateRoleCommand extends Command
{
     /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'create:role {roleName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new role.';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function fire()
    {
        $this->info('Creating a new role');

        $roleEntity = new Role($this->argument('roleName'));

        $role = app()
            ->make('em')
            ->getRepository('\ApiArchitect\Auth\Entities')
            ->store($roleEntity);
    }
}