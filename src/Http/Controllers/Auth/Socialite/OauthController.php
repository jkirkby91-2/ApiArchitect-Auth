<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth\Socialite;

use Socialite;
use Tymon\JWTAuth\JWTAuth;
use ApiArchitect\Auth\Entities\User;
use Laravel\Socialite\SocialiteManager;
use Psr\Http\Message\ServerRequestInterface;
use ApiArchitect\Auth\Contracts\SocialiteOauthContract;
use Spatie\Fractal\ArraySerializer AS ArraySerialization;
use Jkirkby91\LumenRestServerComponent\Libraries\ResourceResponseTrait;
use Jkirkby91\LumenRestServerComponent\Http\Controllers\RestController;
use Jkirkby91\Boilers\RestServerBoiler\TransformerContract AS ObjectTransformer;
use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;

class OauthController extends RestController implements SocialiteOauthContract
{

    use ResourceResponseTrait;

    protected $socialiteManager;

    protected $repository;

    /**
     * @var $auth
     */
    protected $auth;

    /**
     * @var ObjectTransformer
     */
    protected $transformer;

    /**
     * OauthController constructor.
     * @param SocialiteManager $socialiteManager
     */
    public function __Construct(SocialiteManager $socialiteManager, ResourceRepository $repository, JWTAuth $auth, ObjectTransformer $objectTransformer)
    {
      $this->auth = $auth;
      $this->socialiteManager = $socialiteManager;
      $this->repository = $repository;
      $this->transformer = $objectTransformer;
    }

    /**
     * Redirect the user to the OAuth Provider.
     *
     * @return Response
     */
    public function redirectToProvider($provider)
    {
      return $this->socialiteManager->with($provider)->stateless()->redirect()->getTargetUrl();
    }

    /**
     * Obtain the user information from provider.  Check if the user already exists in our
     * database by looking up their provider_id in the database.
     * If the user exists, log them in. Otherwise, create a new user then log them in. After that 
     * redirect them to the authenticated users homepage.
     *
     * @return Response
     */
    public function handleProviderCallback(ServerRequestInterface $request, $provider)
    {
      $payload = $request->getParsedBody();

      $token = $this->socialiteManager->with($provider)->stateless()->getAccessTokenResponse($payload['code']);

      $oauthUser = $this->socialiteManager->with($provider)->stateless()->userFromToken($token['access_token']);

      $userEntity = $this->repository->findOrCreateOauthUser($oauthUser);

      $providerEntity = app()
        ->make('em')
        ->getRepository('\ApiArchitect\Auth\Entities\Social\Provider')
        ->findOneBy(['name' => $provider]);

      if(is_null($providerEntity))
      {
        throw new \Exception('Oauth Provider not found');
      }

      $socialAccountEntity = app()
        ->make('em')
        ->getRepository('\ApiArchitect\Auth\Entities\Social\SocialAccount')
        ->findOrCreateSocialAccount($providerEntity, $oauthUser, $userEntity);  

      $token = $this->auth->fromUser($userEntity);
      
      $resource = $this->item($token)
          ->transformWith($this->transformer)
          ->serializeWith(new ArraySerialization())
          ->toArray();

      return $this->showResponse($resource);
    }
}
