<?php

namespace App\Http\Controllers\Auth;

use App\Models\Auth\Role;
use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Auth;
use App\Services\Auth\AuthService;
use App\Http\Rules\{LoginRequest, GenerateAccessTokenRequest, CreateUserRequest};
use App\Http\Controllers\Controller;
use Illuminate\Auth\AuthenticationException;

class AuthController extends Controller
{
    protected $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function register(CreateUserRequest $request)
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'message' => 'User created successfully',
                    'data'    => $this->authService->createUser($request->validated())
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnIntenalServerErrorResponse($e->getMessage());
        }
    }

    public function login(LoginRequest $request)
    {
        try {
            $data = $this->authService->mobileLogin($request->validated());
            $data['token'] = $this->authService->generateUserTokens($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $data
                ],
                JsonResponse::HTTP_OK,
            );

        } catch (AuthenticationException $e) {
            return returnUnauthorizedResponse($e->getMessage());
        } catch (\Exception $e) {
            return returnIntenalServerErrorResponse($e->getMessage());
        }
    }


    public function webLogin(LoginRequest $request)
    {
        try {
            $data = $this->authService->webLogin($request->validated());
            $data['token'] = $this->authService->generateUserTokens($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'data'    => $data
                ],
                JsonResponse::HTTP_OK,
            );

        } catch (\Exception $e) {
            return returnIntenalServerErrorResponse($e->getMessage());
        }
    }

    public function checkWebAppAccess($user)
    {
        if ($user->hasPermissionTo('Web app access')) {
            return true;
        }
        return false;
    }

    public function generateAccessToken(GenerateAccessTokenRequest $request)
    {
        try {
            $token = $this->authService->refreshUserTokens($request->validated());
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'token' => $token,
                    ]
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (AuthenticationException $e) {
            return returnUnauthorizedResponse($e->getMessage());
        } catch (\Exception $e) {
            return returnIntenalServerErrorResponse($e->getMessage());
        }
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        if ($user) {
            $user->token()->revoke();
        }
        return returnResponse(
            [
                'success' => true,
                'message' => 'Logged out'
            ],
            JsonResponse::HTTP_OK,
        );
    }
}
