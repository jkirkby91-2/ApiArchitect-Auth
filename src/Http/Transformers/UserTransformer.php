<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Transformers {

		use ApiArchitect\{
			Compass\Http\Transformers\AbstractTransformer
		};

		/**
		 * Class UserTransformer
		 *
		 * @package ApiArchitect\Auth\Http\Transformers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class UserTransformer extends AbstractTransformer
		{
			/**
			 * transform()
			 * @param $user
			 *
			 * @return array
			 */
			public function transform($user) : array
			{
				$rolesCollection = $user->getRoles();
				$rolesArray = [];

				foreach ($rolesCollection as $role) {
					array_push($rolesArray,$role->getName());
				};

				return [
					'status'    => 'success',
					'data' => [
						'uid'           => $user->getId(),
						'avatar'        => $user->getAvatar(),
						'firstName'     => $user->getFirstName(),
						'lastName'      => $user->getLastName(),
						'email'         => $user->getEmail(),
						'username'      => $user->getUsername(),
						'roles'         => $rolesArray
					],
				];
			}
		}
	}
