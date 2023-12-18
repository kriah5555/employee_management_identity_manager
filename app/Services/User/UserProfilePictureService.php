<?php

namespace App\Services\User;

use App\Models\User\User;
use App\Models\User\UserProfilePicture;
use Illuminate\Support\Facades\Storage;

class UserProfilePictureService
{


    public function updateUserProfilePicture($values, User $user)
    {
        try {
            $attachmentPath = null;
            $originalFileName = null;

            if ($values->hasFile('image_path')) {
                $values->validate([
                    'image_path' => 'file',
                ]);

                // Get the original file name
                $originalFileName = $values->file('image_path')->getClientOriginalName();

                $newFileName = $user->username . '.' . $values->file('image_path')->getClientOriginalExtension();

                $attachmentPath = $values->file('image_path')->storeAs('userprofiles', $newFileName, 'public');
            }

            $oldFileName = UserProfilePicture::where('user_id', $user->id)->value('image_name');

            // Update or create message
            UserProfilePicture::updateOrCreate(
                ['user_id' => $user->id],
                ['image_name' => $originalFileName, 'image_path' => $attachmentPath]
            );

            $response = ['success' => true, 'message' => 'Profile picture updated successfully'];

            if ($attachmentPath && $oldFileName) {
                $response['data']['file_path'] = asset('storage/' . $attachmentPath);

                // Delete old file if it exists
                $oldFilePath = storage_path('app/public/userprofiles/' . $oldFileName);
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            return response()->json($response, 200);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }







    public function userProfilePictureById($userId)
    {
        $userProfile = UserProfilePicture::where('user_id', $userId)->first();

        if (!$userProfile) {
            return ['success' => false, 'message' => 'User profile picture not found'];
        }

        if($userProfile->image_path){
        $response['image_path'] = asset('storage/' . $userProfile->image_path);
        }else{
          $response=  null;
        }

        return $response;
    }

    public function deleteUserProfilePictureById($userId)
    {
        $userProfile = UserProfilePicture::where('user_id', $userId)->first();

        if (!$userProfile) {
            return ['success' => false, 'message' => 'User profile picture not found'];
        }

        try {
            // Delete the file using Storage::delete
            $success = Storage::delete('public/userprofiles/' . $userProfile->image_path);

            if (!$success) {
                return ['success' => false, 'message' => 'Error deleting file'];
            }
        } catch (\Exception $e) {
            return ['success' => false, 'message' => 'Error deleting file: ' . $e->getMessage()];
        }

        // Delete the record from the database
        $userProfile->delete();

        return response()->json(['success' => true, 'message' => 'User profile picture deleted'], 200);
    }


}
