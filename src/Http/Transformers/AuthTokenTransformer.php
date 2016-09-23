<?php

namespace ApiArchitect\Auth\Http\Transformers;

use Jkirkby91\Boilers\RestServerBoiler\TransformerContract;

use League\Fractal\TransformerAbstract;

/**
 * Class AuthTokenTransformer
 * @package ApiArchitect\Auth\Http\Transformers
 */
class AuthTokenTransformer extends TransformerAbstract implements TransformerContract
{
    public function transform($token)
    {
        return [
            'bearer' => $token
        ];
    }
}