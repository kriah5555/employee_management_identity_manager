<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuthService;
use App\Http\Rules\CreateUserRequest;
use App\Http\Rules\LoginRequest;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{

    public function __construct()
    {
       $this->auth_service = new AuthService();
    }

    public function register(CreateUserRequest $request)
    {
        try {
            $user = $this->auth_service->createUser($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        
        return response()->json(['message' => 'User registered successfully']);
    }
    public function login(LoginRequest $request)
    {
        if (Auth::attempt(['username' => $request->validated()['username'], 'password' => $request->validated()['password']])) {
            $user = Auth::user();
            $token = $user->createToken($user->username)->accessToken;
            $data = [
                'token' => $token,
                'uid' => $user->id,
                'username' => $user->username,
            ];
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], JsonResponse::HTTP_UNAUTHORIZED);
    }

    public function logout(Request $request)
    {
        $user = Auth::guard('api')->user();
        $user->token()->revoke();
        return response()->json([
            'success' => true,
            'message' => 'Logged out'
        ]);
    }

    public function validateAccessToken()
    {
        
    }
}

