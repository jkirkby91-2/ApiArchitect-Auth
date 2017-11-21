<?php

	namespace ApiArchitect\Auth\Http\Transformers {

		use ApiArchitect\{
			Compass\Http\Transformers\AbstractTransformer
		};

		/**
		 * Class UserTransformer
		 *
		 * @package ApiArchitect\Auth\Http\Transformers
		 * @author James Kirkby <me@jameskirkby.com>
		 */
		class UserTransformer extends AbstractTransformer
		{
			/**
			 * @param \ApiArchitect\Auth\Entities\User $user
			 * @return array
			 */
			public function transform($user)
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