<?php

namespace ApiArchitect\Auth\Http\Middleware;

use Closure;
use Illuminate\Contracts\Auth\Guard;
use ApiArchitect\Compass\Http\Middleware\AbstractMiddleware;

/**
 * Class RedirectIfAuthenticated
 * @package app\Middleware
 * @TODO do we need this?
 */
class RedirectIfAuthenticated extends AbstractMiddleware
{
    /**
     * The Guard implementation.
     *
     * @var Guard
     */
    protected $auth;

    /**
     * Create a new filter instance.
     *
     * @param  Guard  $auth
     * @return void
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
        if ($this->auth->check()) {
            return redirect('/home');
        }

        return $next($request);
    }
}