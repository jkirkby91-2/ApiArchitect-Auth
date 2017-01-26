<?php


namespace ApiArchitect\Auth\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface JWTParserContract
{
    /**
     * Parse the request.
     *
     * @param  \Psr\Http\Message\ServerRequestInterface  $request
     *
     * @return null|string
     */
    public function parse(ServerRequestInterface $request);
}
