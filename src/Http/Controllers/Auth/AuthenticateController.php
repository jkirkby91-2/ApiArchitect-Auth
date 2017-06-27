<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth;

use ApiArchitect\Auth\ApiArchitectAuth;
use Tymon\JWTAuth\JWTAuth;
use Doctrine\ORM\EntityNotFoundException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Psr\Http\Message\ServerRequestInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Spatie\Fractal\ArraySerializer AS ArraySerialization;
use ApiArchitect\Auth\Contracts\JWTAuthControllerContract;
use Jkirkby91\LumenRestServerComponent\Libraries\ResourceResponseTrait;
use Jkirkby91\LumenRestServerComponent\Http\Controllers\RestController;
use Jkirkby91\Boilers\RestServerBoiler\TransformerContract AS ObjectTransformer;
use Jkirkby91\Boilers\RepositoryBoiler\ResourceRepositoryContract AS ResourceRepository;

/**
 * Class AuthenticateController
 *
 * @package app\Http\Controllers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class AuthenticateController extends RestController implements JWTAuthControllerContract
{

    use ResourceResponseTrait;

    /**
     * @var $auth
     */
    protected $auth;

    /**
     * @var
     */
    protected $token;

    /**
     * @var ObjectTransformer
     */
    protected $authTokenTransformer;

    /**
     * @var ObjectTransformer
     */
    protected $userTransformer;

    protected $repository;    

    /**
     * AuthenticateController constructor.
     * @param JWTAuth $auth
     */
    public function __construct(ApiArchitectAuth $auth, ResourceRepository $repository, ObjectTransformer $authTokenTransformer, ObjectTransformer $userTransformer)
    {
        $this->auth = $auth;
        $this->repository = $repository;
        $this->authTokenTransformer = $authTokenTransformer;
        $this->userTransformer = $userTransformer;
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     */
    public function authenticate(ServerRequestInterface $request)
    {
        try {
          if (! $this->token = $this->auth->attempt($request->getParsedBody())) {
              return $this->UnauthorizedResponse();
          }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
          return $this->clientErrorResponse();
        } catch (EntityNotFoundException $e) {
          return $this->notFoundResponse();
        }

        $token = $this->item($this->token)
            ->transformWith($this->authTokenTransformer)
            ->serializeWith(new ArraySerialization())
            ->toArray();

        return $this->showResponse($token);
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool|\Zend\Diactoros\Response\JsonResponse
     */
    public function logout(ServerRequestInterface $request)
    {
        $this->token = $request->getParsedBody();

        try {
            $resource = $this->auth->invalidate();
        } catch (JWTException $e) {
            return $this->clientErrorResponse();
        }
        return $resource;
    }

    /**
     * Returns the authenticated user
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticatedUser()
    {
        try {
            if (!$this->user = $this->auth->parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $user = $this->item($this->user)
            ->transformWith($this->userTransformer)
            ->serializeWith(new ArraySerialization())
            ->toArray();

        return $this->showResponse($user);
    }

    /**
     * Refresh the token
     *
     * @return mixed
     */
    public function getToken()
    {
        $token = $this->auth->getToken();
        if (!$token) {
            return $this->response->errorMethodNotAllowed('Token not provided');
        }

        try {
            $refreshedToken = $this->auth->refresh($token);
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return $this->clientErrorResponse('Not able to refresh Token');
        }

        $token = $this->item($refreshedToken)
            ->transformWith($this->authTokenTransformer)
            ->serializeWith(new ArraySerialization())
            ->toArray();

        return $this->showResponse($token);
    }

    /**
     * @return \Zend\Diactoros\Response\JsonResponse
     */
    public function user()
    {
      $this->user = $this->repository->find($this->auth->getPayload()->get('sub'));

        $user = $this->item($this->user)
            ->transformWith($this->userTransformer)
            ->serializeWith(new ArraySerialization())
            ->toArray();

        return $this->showResponse($user);
    }

    /**
     * @return \Zend\Diactoros\Response\JsonResponse
     */
    public function refresh()
    {
        return $this->getToken();
    }
}
