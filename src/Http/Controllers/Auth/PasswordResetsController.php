<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Controllers\Auth {

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use ApiArchitect\{
			Auth\Entities\PasswordResets, Auth\Libraries\PasswordReset
		};

		use Symfony\{
			Component\Finder\Exception\AccessDeniedException
		};

		use Jkirkby91\{
			Boilers\RestServerBoiler\Exceptions\NotFoundHttpException
		};

		use Zend\{
			Diactoros\Response\JsonResponse
		};

		/**
		 * Class PasswordResetsController
		 *
		 * @package ApiArchitect\Auth\Http\Controllers\Auth
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class PasswordResetsController extends AuthenticateController
		{

			use PasswordReset;

			/**
			 * reset()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function reset(ServerRequestInterface $request) : JsonResponse
			{
				$data = $request->getParsedBody();

				$user = app()->make('em')->getRepository('\ApiArchitect\Auth\Entities\User')->findOneBy(['email' => $data['email']]);

				//@TODO check email exists wrap this in condition logic
				if(!is_null($user))
				{
					$passwordReset = new PasswordResets($user,$this->generateToken(),0);

					//@TODO some try catch magic
					$passwordReset = app()->make('em')->getRepository('\ApiArchitect\Auth\Entities\PasswordResets')->create($passwordReset);
				}

				//@TODO push password reset email to an email queue
				return $this->showResponse(array("If a matching account was found an email was sent to ".$data['email']." to allow you to reset your password."));
			}

			/**
			 * @param $token
			 * @return \Zend\Diactoros\Response\JsonResponse
			 */
			public function verify(string $token) : JsonResponse
			{
				$passwordResetEntity = app()->make('em')->getRepository('\ApiArchitect\Auth\Entities\PasswordResets')->findOneBy(['token' => $token,'used' => 0]);

				if(is_null($passwordResetEntity))
				{
					return $this->notFoundResponse();
				}

				if ($passwordResetEntity->getUsed() === true)
				{
					return $this->unauthorizedResponse();
				}

				$passwordResetEntity = $passwordResetEntity->setUsed(1);

				$passwordResetEntity = app()->make('em')->getRepository('\ApiArchitect\Auth\Entities\PasswordResets')->update($passwordResetEntity);

				$itemResource = $this->item($this->auth->fromUser($passwordResetEntity->getUser()))
					->transformWith($this->authTokenTransformer)
					->serializeWith($this->serializer)
					->toArray();

				return $this->showResponse($itemResource);
			}
		}
	}
