<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth;

use ApiArchitect\Compass\Http\Controllers\RestApi;
use Doctrine\ORM\EntityNotFoundException;
use Tymon\JWTAuth\JWTAuth;
use Psr\Http\Message\ServerRequestInterface;
use Tymon\JWTAuth\Exceptions\JWTException;
use Spatie\Fractal\ArraySerializer AS ArraySerialization;
use ApiArchitect\Auth\Contracts\JWTAuthControllerContract;
use Jkirkby91\LumenRestServerComponent\Http\Controllers\ResourceController;

/**
 * Class AuthenticateController
 *
 * @package app\Http\Controllers
 * @author James Kirkby <jkirkby91@gmail.com>
 */
class AuthenticateController extends RestApi implements JWTAuthControllerContract
{

    /**
     * @var $auth
     */
    protected $auth;

    /**
     * @var
     */
    protected $token;

    /**
     * AuthenticateController constructor.
     * @param JWTAuth $auth
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
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

        return $this->showResponse(fractal()
            ->item($this->token)
            ->transformWith(new \ApiArchitect\Auth\Http\Transformers\AuthTokenTransformer())
            ->serializeWith(new ArraySerialization())
            ->toArray()
        );
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
        return $this->showResponse(fractal()
            ->item($this->user)
            ->transformWith(new \ApiArchitect\Compass\Http\Transformers\UserTransformer())
            ->serializeWith(new ArraySerialization())
            ->toArray()
        );
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

        return $this->createdResponse(['token' => $refreshedToken]);
    }

    /**
     * @return \Zend\Diactoros\Response\JsonResponse
     */
    public function user()
    {
        $this->user = fractal()
            ->item(app()
                ->make('em')
                ->getRepository('\ApiArchitect\Compass\Entities\User')
                ->find($this->auth->getPayload()->get('sub'))
            )
            ->transformWith(new \ApiArchitect\Compass\Http\Transformers\UserTransformer())
            ->serializeWith(new ArraySerialization());

        return $this->showResponse($this->user);
    }

    /**
     * @return \Zend\Diactoros\Response\JsonResponse
     */
    public function refresh()
    {
        return $this->getToken();
    }
}
