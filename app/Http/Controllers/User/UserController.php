<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Rules\ForgotPassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Services\User\{UserService, GenderService, MaritalStatusService};
use App\Http\Rules\{CreateEditEmployee, CreateUserRequest, InviteEmployee, UpdateEmployeeRule};


class UserController extends Controller
{
    protected $userService;

    protected $genderService;

    protected $maritalStatusService;

    /**
     * class constructor
     */

    public function __construct(UserService $userService, GenderService $genderService, MaritalStatusService $maritalStatusService)
    {
        $this->userService = $userService;
        $this->genderService = $genderService;
        $this->maritalStatusService = $maritalStatusService;
    }

    public function createUser(CreateUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->createUser($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => $user
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
        $data = $this->userService->manageUserService($userid, $supervisor);

        if ($data) {
            return response()->json([
                'status'  => 200,
                'message' => "User fetched successfully",
                'data'    => $data
            ], 200);
        } else {
            return response()->json([
                'status'  => 500,
                'message' => "Error in fetching the user"
            ], 500);
        }
    }

    public function getUserDetails()
    {
        $user = Auth::guard('api')->user();
        return response()->json([
            'success' => true,
            'data'    => $user
        ], 200);
    }

    public function getEmployeeCreationOptions()
    {
        return UserService::getEmployeeOptionsService();
    }


    public function createEmployee(CreateEditEmployee $employee_details)
    {
        try {
            $inputData = $employee_details->validated();
            $user = $this->userService->createEmployeeService($inputData);
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => $user
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
            $user = $this->userService->inviteEmployee($invite_employee->validated());
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data'    => $user
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function create()
    {
        try {
            return returnResponse(
                [
                    'success' => true,
                    'data'    => [
                        'genders'                  => $this->genderService->getActiveGenders(),
                        'marital_statuses'         => $this->maritalStatusService->getActiveMaritalStatuses(),
                        'dependent_spouse_options' => associativeToDictionaryFormat($this->userService->getDependentSpouseOptions(), 'key', 'value'),
                        'languages'                => associativeToDictionaryFormat($this->userService->getLanguageOptions(), 'key', 'value'),
                    ]
                ],
                JsonResponse::HTTP_OK,
            );
        } catch (\Exception $e) {
            return returnResponse(
                [
                    'success' => false,
                    'data'    => [
                        'success' => false,
                        'message' => $e->getMessage(),
                    ]
                ],
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR,
            );
        }
    }
    public function getDependentSpouseOptions()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->userService->getDependentSpouseOptions(),
            ],
            JsonResponse::HTTP_OK,
        );
    }
    public function getLanguageOptions()
    {
        return returnResponse(
            [
                'success' => true,
                'data'    => $this->userService->getLanguageOptions(),
            ],
            JsonResponse::HTTP_OK,
        );
    }


    public function forgotPassword(ForgotPassword $request)
    {

            $messages = $this->userService->forgotPassword($request->validated());
            return $messages;

    }


    public function resetPassword(ForgotPassword $request)
    {
        $messages = $this->userService->resetPassword($request->validated());
        return $messages;
    }




}
