<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Libraries {

		/**
		 * Trait PasswordReset
		 *
		 * @package ApiArchitect\Auth\Libraries
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		trait PasswordReset
		{

			/**
			 * generateToken()
			 * @param int $length
			 *
			 * @return string
			 */
			public function generateToken(int $length=25) : string
			{
				return str_random($length);
			}

			/**
			 * @TODO implement
			 */
			public function buildPasswordResetEmailObject(){}
		}
	}