<?php

namespace ApiArchitect\Auth\Http\Requests;

use Psr\Http\Message\ServerRequestInterface;
use ApiArchitect\Compass\Http\Requests\AbstractValidateRequest;

/**
 * Class UserRequest
 *
 * @package ApiArchitect\Auth\Http\Requests
 * @author James Kirkby <hello@jameskirkby.com>
 */
class UserRequest extends AbstractValidateRequest
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     * @author James Kirkby <hello@jameskirkby.com>
     */
    public function rules()
    {
        return [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:ApiArchitect\Auth\Entities\User,email',
            'username' => 'required|max:255|unique:ApiArchitect\Auth\Entities\User,username',
            'password' => 'required|confirmed|min:8',
        ];
    }
}