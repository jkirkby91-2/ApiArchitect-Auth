<?php

namespace ApiArchitect\Auth\Console\Commands;

use Illuminate\Console\Command;
use ApiArchitect\Auth\Entities\Social\Provider;

class CreateProviderCommand extends Command
{
     /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'create:provider {providerName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new provider.';

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function fire()
    {
        $this->info('Creating a new provider');

        $providerEntity = new Provider($this->argument('providerName'));

        $role = app()
            ->make('em')
            ->getRepository('\ApiArchitect\Auth\Entities\Social\Provider')
            ->store($providerEntity);
    }
}