<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth;

use ApiArchitect\Auth\Entities\PasswordResets;
use ApiArchitect\Auth\Libraries\PasswordReset;
use Jkirkby91\LumenRestServerComponent\Http\Controllers\CrudController;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Class PasswordController
 *
 * @package ApiArchitect\Auth\Http\Controllers\Auth
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class PasswordResetsController extends CrudController
{

    use PasswordReset;

    /**
     * @param ServerRequestInterface $request
     */
    public function reset(ServerRequestInterface $request)
    {
        $data = $request->getParsedBody();

        $passwordReset = new PasswordResets($data['email'],$this->generateToken(),0);

        //@TODO some try catch magic
        $passwordReset = $this->repository->create($passwordReset);

        return $this->showResponse(['If a matching account was found an email was sent to '.$data['email'].' to allow you to reset your password.']);
    }

    /**
     * @param $token
     */
    public function verify($token)
    {
        $passwordResetEntity = $this->repository->read($token);

        dd($passwordResetEntity);
    }
}