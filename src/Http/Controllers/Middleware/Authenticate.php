<?php

namespace ApiArchitect\Auth\Http\Controllers\Middleware;

use Closure;
use Tymon\JWTAuth\JWTAuth;
use Psr\Http\Message\ServerRequestInterface;

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
     *
     */
    protected $user;

    /**
     * Authenticate constructor.
     * @param Guard $auth
     */
    public function __construct(JWTAuth $auth)
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
    public function handle(ServerRequestInterface $request, Closure $next)
    {
               // dd($request->getParsedBody());

        $this->user = $this->auth->user();

        if ($this->auth->guest()) {
            if ($request->ajax()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect('auth/login');
            }
        }
        return $next($request);
    }
}