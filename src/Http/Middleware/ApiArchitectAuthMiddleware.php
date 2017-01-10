<?php

namespace ApiArchitect\Auth\Http\Middleware;

use Closure;
use Zend\Diactoros\ServerRequest;
use Tymon\JWTAuth\Http\Middleware\Authenticate;

class ApiArchitectAuthMiddleware extends Authenticate
{

    public $test;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $this->test = 'trace123';
        return $next($request);
    }
}
