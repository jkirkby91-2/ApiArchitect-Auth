<?php

namespace ApiArchitect\Auth\Http\Requests;

use Psr\Http\Message\ServerRequestInterface;
use ApiArchitect\Compass\Http\Requests\AbstractValidateRequest;
use Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnprocessableEntityException;

/**
 * Class UserRequest
 *
 * @package ApiArchitect\Auth\Http\Requests
 * @author James Kirkby <hello@jameskirkby.com>
 */
class UserRegistrationRequest extends AbstractValidateRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author James Kirkby <hello@jameskirkby.com>
     */
    public function rules(ServerRequestInterface $request)
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
          'name' => 'required|max:255',
          'email' => 'required|email|max:255|unique:ApiArchitect\Auth\Entities\User,email',
          'username' => 'required|max:255|unique:ApiArchitect\Auth\Entities\User,username',
          'password' => 'required|min:8',
          'role'    => 'required|exists:ApiArchitect\Auth\Entities\Role,name'
        ]
      ];
    }
}