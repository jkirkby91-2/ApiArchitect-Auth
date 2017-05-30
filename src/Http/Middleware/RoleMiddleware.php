<?php

namespace ApiArchitect\Auth\Http\Middleware;

use Closure;
use Psr\Http\Message\ServerRequestInterface;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use ApiArchitect\Compass\Http\Middleware\AbstractMiddleware;

class RoleMiddleware extends AbstractMiddleware
{

    /**
     * Run the request filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(ServerRequestInterface $request, Closure $next, $role)
    {
        $user = $this->auth->parseToken()->authenticate();
        if (!$user->hasRole($role)) {
            return redirect('/login');
        }

        return $next($request);
    }

}