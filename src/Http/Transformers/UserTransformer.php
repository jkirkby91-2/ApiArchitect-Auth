<?php

namespace ApiArchitect\Auth\Http\Transformers;

use League\Fractal\TransformerAbstract;
use Jkirkby91\Boilers\RestServerBoiler\TransformerContract;

/**
 * Class UserTransformer
 *
 * @package ApiArchitect\Auth\Http\Transformers
 * @author James Kirkby <me@jameskirkby.com>
 */
class UserTransformer extends TransformerAbstract implements TransformerContract
{
    /**
     * @param $user
     * @return array
     */
    public function transform($user)
    {
        $name = json_decode($user->getName(),true);
        $rolesCollection = $user->getRoles();
        $rolesArray = [];

        foreach ($rolesCollection as $role) {
            array_push($rolesArray,$role->getName());
        };

        return [
            'status'    => 'success',
            'data' => [
                'uid'           => $user->getId(),
                'avatar'        => 'https://fuuse.net/wp-content/uploads/2016/02/avatar-placeholder.png',
                'firstname'     => $name['firstname'],
                'lastname'      => $name['lastname'],
                'email'         => $user->getEmail(),
                'username'      => $user->getUserName(),
                'roles'         => $rolesArray
            ],
        ];
    }

}