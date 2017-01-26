<?php

namespace ApiArchitect\Auth\Contracts;

use Psr\Http\Message\ServerRequestInterface;

interface JWTRequestParserContract
{

    /**
     * Get the parser chain.
     *
     * @return array
     */
    public function getChain();

    /**
     * Set the order of the parser chain.
     *
     * @param  array  $chain
     *
     * @return $this
     */
    public function setChain(array $chain);

    /**
     * Alias for setting the order of the chain.
     *
     * @param  array  $chain
     *
     * @return $this
     */
    public function setChainOrder(array $chain);

    /**
     * Iterate through the parsers and attempt to retrieve
     * a value, otherwise return null.
     *
     * @return string|null
     */
    public function parseToken();

    /**
     * Check whether a token exists in the chain.
     *
     * @return bool
     */
    public function hasToken();

    /**
     * Set the request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    public function setRequest(ServerRequestInterface $request);

}
