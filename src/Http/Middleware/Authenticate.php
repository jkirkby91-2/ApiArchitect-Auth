<?php

namespace ApiArchitect\Auth\Http\Middleware;

use Closure;
use Tymon\JWTAuth\JWTAuth;
use ApiArchitect\Auth\Http\Parser\Parser;
use Tymon\JWTAuth\Exceptions\JWTException;
use Psr\Http\Message\ServerRequestInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

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
     * The ApiArchitect User
     */
    protected $user;


    protected $parser;

    /**
     * Authenticate constructor.
     * @param Guard $auth
     */
    public function __construct(JWTAuth $auth, Parser $parser)
    {
        $this->auth = $auth;
        $this->parser = $parser;
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
        $this->user = $this->authenticate($request);

        return $next($request);
    }

    /**
     * Check the request for the presence of a token.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return void
     */
    public function checkForToken(ServerRequestInterface $request)
    {
        if (! $this->parser->setRequest($request)->hasToken()) {
            throw new BadRequestHttpException('Token not provided');
        }
    }

    /**
     * Attempt to authenticate a user via the token in the request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return \ApiArchitect\Copass\User
     */
    public function authenticate(ServerRequestInterface $request)
    {
        $this->checkForToken($request);

        try {
            return $this->auth->parseToken()->authenticate();
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('ApiArchitect.Auth', $e->getMessage(), $e, $e->getCode());
        }
    }    
}