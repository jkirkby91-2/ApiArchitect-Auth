<?php
	declare(strict_types=1);

	namespace ApiArchitect\Auth\Http\Transformers {

		use ApiArchitect\{
			Compass\Http\Transformers\AbstractTransformer
		};

		/**
		 * Class AuthTokenTransformer
		 *
		 * @package ApiArchitect\Auth\Http\Transformers
		 * @author  James Kirkby <jkirkby@protonmail.ch>
		 */
		class AuthTokenTransformer extends AbstractTransformer
		{
			/**
			 * transform()
			 * @param $object
			 *
			 * @return array
			 */
			public function transform($object) : array
			{
				return [
					'token' => $object
				];
			}
		}
	}
