<?php

namespace App\Http\Controllers\User;

use App\Models\User\User; // Add this line if not already present
use App\Services\User\UserProfilePictureService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class UserProfilePictureController extends Controller
{
    protected $userProfilePictureService;

    public function __construct(UserProfilePictureService $userProfilePictureService)
    {
        $this->userProfilePictureService = $userProfilePictureService;
    }

    public function getEmployeeProfilePicture()
    {
        $userID = Auth::guard('api')->user()->id;
        try {
        $employeeProfilePicture = $this->userProfilePictureService->userProfilePictureById($userID);

            return response()->json([
                'success' => true,
                'data'    => $employeeProfilePicture
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return $employeeProfilePicture;
    }

    public function updateEmployeeProfilePicture(Request $request)
    {
        $user = Auth::guard('api')->user();
        $updateEmployeeProfilePicture = $this->userProfilePictureService->updateUserProfilePicture($request, $user);
        return $updateEmployeeProfilePicture;
    }

    public function deleteEmployeeProfilePicture()
    {
        $userId = Auth::guard('api')->user()->id;
        $updateEmployeeProfilePicture = $this->userProfilePictureService->deleteUserProfilePictureById($userId);
        return $updateEmployeeProfilePicture;
    }
}
