<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Console\Commands {

		use Illuminate\{
			Console\Command
		};

		use ApiArchitect\{
			Auth\Entities\Role
		};

		/**
		 * Class CreateRoleCommand
		 *
		 * @package ApiArchitect\Auth\Console\Commands
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
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
			 * fire()
			 * @return bool
			 */
			public function fire() : bool
			{
				$this->info('Creating a new role');

				$roleEntity = new Role($this->argument('roleName'));

				$role = app()
					->make('em')
					->getRepository('\ApiArchitect\Auth\Entities\Role')
					->store($roleEntity);

				return true;
			}
		}
	}
