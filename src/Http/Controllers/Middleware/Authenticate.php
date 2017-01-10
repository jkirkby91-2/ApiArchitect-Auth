<?php

namespace ApiArchitect\Auth\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;

/**
 * Class Authenticate
 * @package ApiArchitect\Auth\Http\Middleware
 * @TODO do we need this?
 */
class Authenticate
{

    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Authenticate constructor.
     * @param Guard $auth
     */
    public function __construct(Guard $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('auth/login');
            }
        }
        return $next($request);
    }
}