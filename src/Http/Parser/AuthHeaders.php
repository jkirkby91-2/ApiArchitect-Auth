<?php

namespace ApiArchitect\Auth\Http\Parser;

use Psr\Http\Message\ServerRequestInterface;
use ApiArchitect\Auth\Contracts\JWTParserContract;

class AuthHeaders implements JWTParserContract
{

    /**
     * The header name.
     *
     * @var string
     */
    protected $header = 'authorization';

    /**
     * The header prefix.
     *
     * @var string
     */
    protected $prefix = 'bearer';

    /**
     * Attempt to parse the token from some other possible headers.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return null|string
     */
    protected function fromAltHeaders(ServerRequestInterface $request)
    {
        // dd($request->getHeaders());
        return $request->getHeader('HTTP_AUTHORIZATION') ?: $request->getHeader('REDIRECT_HTTP_AUTHORIZATION');
    }

    /**
     * Try to parse the token from the request header.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return null|string
     */
    public function parse(ServerRequestInterface $request)
    {
// echo(123);dd($request);
        $header = $request->getHeader($this->header) ?: $this->fromAltHeaders($request);
        //@TODO some logic to make sure we onlyget one header, or check each header that matches

        if ($header[0] && stripos($header[0], $this->prefix) === 0) {
            return trim(str_ireplace($this->prefix, '', $header[0]));
        }
    }

    /**
     * Set the header name.
     *
     * @param  string  $headerName
     *
     * @return $this
     */
    public function setHeaderName($headerName)
    {
        $this->header = $headerName;

        return $this;
    }

    /**
     * Set the header prefix.
     *
     * @param  string  $headerPrefix
     *
     * @return $this
     */
    public function setHeaderPrefix($headerPrefix)
    {
        $this->prefix = $headerPrefix;

        return $this;
    }
}
