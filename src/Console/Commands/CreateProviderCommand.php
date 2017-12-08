<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Console\Commands {

		use Illuminate\{
			Console\Command
		};

		use ApiArchitect\{
			Auth\Entities\Social\Provider
		};

		/**
		 * Class CreateProviderCommand
		 *
		 * @package ApiArchitect\Auth\Console\Commands
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
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
			 * fire()
			 * @return bool
			 */
			public function fire() : bool
			{
				$this->info('Creating a new provider');

				$providerEntity = new Provider($this->argument('providerName'));

				$role = app()
					->make('em')
					->getRepository('\ApiArchitect\Auth\Entities\Social\Provider')
					->store($providerEntity);

				return true;
			}
		}
	}