<?php

namespace ApiArchitect\Auth\Http\Transformers;

use ApiArchitect\Compass\Http\Transformers\AbstractTransformer;

/**
 * Class AuthTokenTransformer
 * @package ApiArchitect\Auth\Http\Transformers
 */
class AuthTokenTransformer extends AbstractTransformer
{
    public function transform($object)
    {
        return [
            'token' => $object
        ];
    }
}