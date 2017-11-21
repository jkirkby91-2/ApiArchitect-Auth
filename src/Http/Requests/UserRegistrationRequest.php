<?php

	namespace ApiArchitect\Auth\Http\Requests;

	use Psr\Http\Message\ServerRequestInterface;
	use ApiArchitect\Compass\Http\Requests\AbstractValidateRequest;
	use Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnprocessableEntityException;

	/**
	 * Class UserRegistrationRequest
	 *
	 * @package ApiArchitect\Auth\Http\Requests
	 * @author  James Kirkby <jkirkby@protonmail.ch>
	 */
	class UserRegistrationRequest extends AbstractValidateRequest
	{

		/**
		 * rules()
		 * @param \Psr\Http\Message\ServerRequestInterface $request
		 *
		 * @return array
		 */
		public function rules(ServerRequestInterface $request) : array
		{
			$userRegDetails = $request->getParsedBody();

			if ($userRegDetails['password'] !== $userRegDetails['passwordConfirm']) {
				throw new UnprocessableEntityException;
			}

			if (!array_key_exists('role', $userRegDetails)) {
				throw new UnprocessableEntityException('No user role for registration specified');
			} else {
				if (in_array($userRegDetails['role'], array('admin','administrator','superadministrator'))){
					throw new UnprocessableEntityException('Un-authorised role type');
				}
			}

			return [
				'POST' => [
					'firstName' => 'required|max:255',
					'lastName' => 'required|max:255',
					'email' => 'required|email|max:255|unique:ApiArchitect\Auth\Entities\User,email',
					'username' => 'required|max:255|unique:ApiArchitect\Auth\Entities\User,username',
					'password' => 'required|min:6',
					'passwordConfirm' => 'required|min:6',
					'role'    => 'required|exists:ApiArchitect\Auth\Entities\Role,name'
				]
			];
		}
	}