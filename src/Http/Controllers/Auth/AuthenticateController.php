<?php

namespace ApiArchitect\Auth\Http\Controllers\Auth;

use Tymon\JWTAuth\JWTAuth;
use Psr\Http\Message\ServerRequestInterface;
use Tymon\JWTAuth\Exceptions\JWTException;

/**
 * Class AuthenticateController
 *
 * @package app\Http\Controllers
 */
class AuthenticateController extends \Jkirkby91\LumenRestServerComponent\Http\Controllers\ResourceController implements \ApiArchitect\Auth\Contracts\JWTAuthControllerContract
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
                throw new \Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnauthorizedHttpException;
            }
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            throw new \Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnprocessableEntityException;
        }

        $itemResource = fractal()
            ->item($this->token)
            ->transformWith(new \ApiArchitect\Auth\Http\Transformers\AuthTokenTransformer())
            ->serializeWith(new \Spatie\Fractal\ArraySerializer())
            ->toArray();

        return $this->showResponse($itemResource);
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    public function logout(ServerRequestInterface $request)
    {

//        $this->validate($request, [
//            'token' => 'required'
//        ]);

        $this->token = $request->getParsedBody();

        try {
            $resource = $this->auth->invalidate();
        } catch (JWTException $e) {
            throw new \Jkirkby91\Boilers\RestServerBoiler\Exceptions\UnprocessableEntityException;
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
            if (!$user = $this->auth->parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (\Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {
            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response()->json(['token_absent'], $e->getStatusCode());
        }
        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
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
            return $this->response->errorInternal('Not able to refresh Token');
        }
        return $this->response->withArray(['token' => $refreshedToken]);
    }
}