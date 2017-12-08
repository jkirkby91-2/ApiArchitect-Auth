<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Parser {

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use ApiArchitect\{
			Auth\Contracts\JWTParserContract
		};

		use Jkirkby91\{
			Boilers\RestServerBoiler\Exceptions\UnauthorizedHttpException
		};

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
			protected $prefix = 'Bearer';

			/**
			 * Attempt to parse the token from some other possible headers.
			 *
			 * @param  \Illuminate\Http\Request  $request
			 *
			 * @return null|string
			 */
			protected function fromAltHeaders(ServerRequestInterface $request)
			{
				return $request->getHeader('HTTP_AUTHORIZATION') ?: $request->getHeader('REDIRECT_HTTP_AUTHORIZATION');
			}

			/**
			 * parse()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return string
			 */
			public function parse(ServerRequestInterface $request) : string
			{
				$header = $request->getHeader($this->header) ?: $this->fromAltHeaders($request);
				//@TODO some logic to make sure we onlyget one header, or check each header that matches

				try {
					if ($header[0] && stripos($header[0], $this->prefix) === 0) {
						return trim(str_ireplace($this->prefix, '', $header[0]));
					}
				} catch (\ErrorException $e) {
					throw new UnauthorizedHttpException;
				}
			}

			/**
			 * setHeaderName()
			 * @param string $headerName
			 *
			 * @return \ApiArchitect\Auth\Http\Parser\AuthHeaders
			 */
			public function setHeaderName(string $headerName) : AuthHeaders
			{
				$this->header = $headerName;

				return $this;
			}

			/**
			 * setHeaderPrefix()
			 * @param string $headerPrefix
			 *
			 * @return \ApiArchitect\Auth\Http\Parser\AuthHeaders
			 */
			public function setHeaderPrefix(string $headerPrefix) : AuthHeaders
			{
				$this->prefix = $headerPrefix;

				return $this;
			}
		}
	}
