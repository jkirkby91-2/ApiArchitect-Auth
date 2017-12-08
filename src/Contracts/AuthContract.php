<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Contracts {

		use ApiArchitect\{
			Auth\Entities\User
		};

		/**
		 * Interface AuthContract
		 *
		 * @package ApiArchitect\Auth\Contracts
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		interface AuthContract
		{
			/**
			 * Check a user's credentials.
			 *
			 * @param  array $credentials
			 *
			 * @return mixed
			 */
			public function byCredentials(array $credentials);

			/**
			 * Authenticate a user via the id.
			 *
			 * @param  mixed $id
			 *
			 * @return mixed
			 */
			public function byId(int $id) : User;

			/**
			 * Get the currently authenticated user.
			 *
			 * @return mixed
			 */
			public function user() : User;
		}
	}
