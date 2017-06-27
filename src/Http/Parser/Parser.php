<?php

namespace ApiArchitect\Auth\Http\Parser;

use Psr\Http\Message\ServerRequestInterface;
use ApiArchitect\Auth\Contracts\JWTRequestParserContract;

class Parser implements JWTRequestParserContract
{

	/**
	 * @var array
	 */
    private $chain;

	/**
	 * @var \Illuminate\Http\Request|\Psr\Http\Message\ServerRequestInterface
	 */
    protected $request;

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  array  $chain
     *
     * @return void
     */
    public function __construct(ServerRequestInterface $request, array $chain = [])
    {
        $this->request = $request;
        $this->chain = $chain;
    }

    /**
     * Get the parser chain.
     *
     * @return array
     */
    public function getChain()
    {
        return $this->chain;
    }

    /**
     * Set the order of the parser chain.
     *
     * @param  array  $chain
     *
     * @return $this
     */
    public function setChain(array $chain)
    {
        $this->chain = $chain;
        return $this;
    }

    /**
     * Alias for setting the order of the chain.
     *
     * @param  array  $chain
     *
     * @return $this
     */
    public function setChainOrder(array $chain)
    {
        return $this->setChain($chain);
    }

	/**
	 * parseToken()
	 *
	 * Iterate through the parsers and attempt to retrieve
	 * a value, otherwise return null.
	 *
	 * @return mixed
	 * @throws \Exception
	 */
    public function parseToken()
    {
		if ($this->chain === array()) {
			throw new \Exception('No Chains');
		}
        foreach ($this->chain as $parser) {
            $response = $parser->parse($this->request);
            if ($response !== null) {
                return $response;
            }
        }
    }

    /**
     * Check whether a token exists in the chain.
     *
     * @return bool
     */
    public function hasToken()
    {
        return $this->parseToken() !== null;
    }

    /**
     * Set the request instance.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return $this
     */
    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;

        return $this;
    }
}
