<?php

namespace ApiArchitect\Auth\Contracts;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface AuthControllerContract
 * @package ApiArchitect\Auth\Contracts
 */
interface JWTAuthControllerContract
{
    public function authenticate(ServerRequestInterface $request);

    /**
     * @param ServerRequestInterface $request
     */
    public function logout(ServerRequestInterface $request);

    /**
     * Returns the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticatedUser();

    /**
     * Refresh the token
     *
     * @return mixed
     */
    public function getToken();


}