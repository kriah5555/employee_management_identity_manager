<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\ForgotPassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Rules\{CreateEditEmployee, CreateUserRequest, InviteEmployee};


class UserController extends Controller
{
    /**
     * creating objects
     */
    protected $user_service;

    /**
     * class constructor
     */

    public function __construct()
    {
        $this->user_service = new UserService();
    }

    public function createUser(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->user_service->createUser($request->validated());
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
    }

    public function manageUsers($userid = null): JsonResponse
    {
        $supervisor = null;
        if ($userid == 'supervisor') {
            $supervisor = $userid;
            $userid = null;
        }
        $data = $this->user_service->manageUserService($userid, $supervisor);

        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => "User fetched successfully",
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => "Error in fetching the user"
            ], 500);
        }
    }

    public function getUserDetails()
    {
        $user = Auth::guard('api')->user();
        return response()->json([
            'success' => true,
            'data' => $user
        ], 200);
    }

    public function getEmployeeCreationOptions()
    {
        return UserService::getEmployeeOptionsService();
    }

    public function updateEmployee()
    {
    }

    public function createEmployee(CreateEditEmployee $employee_details)
    {
        try {
            $inputData = $employee_details->validated();
            $user = $this->user_service->createEmployeeService($inputData);
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
    }

    public function inviteEmployee(InviteEmployee $invite_employee)
    {
        try {
            $user = $this->user_service->inviteEmployee($invite_employee->validated());
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
    }

    public function forgotPassword(ForgotPassword $request)
    {
        $messages = $this->user_service->forgotPassword($request->validated());
        return response()->json(['messages' => $messages]);
    }


    public function resetPassword(ForgotPassword $request)
    {
        $messages = $this->user_service->resetPassword($request->validated());
        return response()->json(['messages' => $messages]);
    }

}
