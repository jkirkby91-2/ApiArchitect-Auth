<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Parser {

		use Psr\{
			Http\Message\ServerRequestInterface
		};

		use ApiArchitect\{
			Auth\Contracts\JWTRequestParserContract
		};

		class Parser implements JWTRequestParserContract
		{

			/**
			 * @var array $chain
			 */
			private $chain;

			/**
			 * @var \Illuminate\Http\Request|\Psr\Http\Message\ServerRequestInterface $request
			 */
			protected $request;

			/**
			 * Parser constructor.
			 *
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 * @param array                                    $chain
			 */
			public function __construct(ServerRequestInterface $request, array $chain = [])
			{
				$this->request = $request;
				$this->chain = $chain;
			}

			/**
			 * getChain()
			 * @return array
			 */
			public function getChain() : array
			{
				return $this->chain;
			}

			/**
			 * setChain()
			 * @param array $chain
			 *
			 * @return \ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function setChain(array $chain) : JWTRequestParserContract
			{
				$this->chain = $chain;
				return $this;
			}

			/**
			 * setChainOrder()
			 * @param array $chain
			 *
			 * @return \ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function setChainOrder(array $chain) : JWTRequestParserContract
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
			public function parseToken() : string
			{
				if ($this->chain === array()) {
					throw new \LogicException('No Chains');
				}
				foreach ($this->chain as $parser) {
					$response = $parser->parse($this->request);
					if ($response !== null) {
						return $response;
					}
				}
			}

			/**
			 * hasToken()
			 * @return bool
			 * @throws \Exception
			 */
			public function hasToken() : bool
			{
				return $this->parseToken() !== null;
			}

			/**
			 * setRequest()
			 * @param \Psr\Http\Message\ServerRequestInterface $request
			 *
			 * @return $this|\ApiArchitect\Auth\Contracts\JWTRequestParserContract
			 */
			public function setRequest(ServerRequestInterface $request) : JWTRequestParserContract
			{
				$this->request = $request;

				return $this;
			}
		}
	}
