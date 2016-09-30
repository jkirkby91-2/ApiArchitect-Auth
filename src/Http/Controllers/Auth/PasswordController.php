<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth;

use ApiArchitect\Compass\Http\Controllers\RestController;
use Illuminate\Foundation\Auth\ResetsPasswords;

class PasswordController extends RestController
{

    use ResetsPasswords;
    //@TODO reset passwords

    /**
     * PasswordController constructor.
     *
     * Create a new password controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}