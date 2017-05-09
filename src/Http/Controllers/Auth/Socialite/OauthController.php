<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth\Socialite;

use Socialite;
use Tymon\JWTAuth\JWTAuth;
use Laravel\Socialite\SocialiteManager;
use ApiArchitect\Auth\Contracts\SocialiteOauthContract;
use ApiArchitect\Auth\Http\Controllers\Auth\AuthenticateController;

class OauthController extends AuthenticateController implements SocialiteOauthContract
{

    protected $socialiteManager;

    /**
     * OauthController constructor.
     * @param SocialiteManager $socialiteManager
     */
    public function __Construct(SocialiteManager $socialiteManager)
    {
      $this->socialiteManager = $socialiteManager;
    }

    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider='facebook')
    {
      return $this->socialiteManager->with($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that 
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback($provider)
    {

    }

}
