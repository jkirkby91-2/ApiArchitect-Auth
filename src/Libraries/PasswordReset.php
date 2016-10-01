<?php

namespace ApiArchitect\Auth\Libraries;

/**
 * Class PasswordReset
 *
 * @package ApiArchitect\Auth\Libraries
 * @author James Kirkby <jkirkby91@gmail.com>
 */
trait PasswordReset
{

    /**
     * generates a random token
     */
    public function generateToken($length=25)
    {
        return str_random($length);
    }
}