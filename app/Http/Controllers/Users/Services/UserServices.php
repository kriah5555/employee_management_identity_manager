<?php

namespace App\Http\Controllers\Users\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Users\User;
use App\Models\Users\Profiles;
use App\Models\Users\UserRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserServices
{
    public function createUserService($request)
    {
        $issaved = false;
        try {
            DB::beginTransaction();
            
            if ($request->edit) {
                $profileUpdateData = [];
                $userData = [];
                if ($request->has('username')) {
                    $userData['username'] = $request->username;
                }
            
                if ($request->has('user_status')) {
                    $userData['status'] = $request->user_status;
                }

                if ($request->has('email')) {
                    $userData['email'] = $request->email;
                }
                if ($request->has('new_password')) {
                    $oldPassword = $request->old_password;
                    $newPassword = $request->new_password;

                    $user = User::find($request->edit);
                    
                    if (Hash::check($oldPassword, $user->password)) {
                        $userData['password'] =  Hash::make($newPassword);
                    }
                }
                
                $updateuser = User::where('user_id', $request->edit)->update($userData);

                if ($request->has('first_name')) {
                    $profileUpdateData['first_name'] = $request->first_name;
                }
            
                if ($request->has('last_name')) {
                    $profileUpdateData['last_name'] = $request->last_name;
                }
            
            
                if ($request->has('supervisor')) {
                    $profileUpdateData['supervisor_id'] = $request->supervisor;
                }
            
                if ($request->has('profile_pic')) {
                    $profileUpdateData['profile_pic_id'] = $request->profile_pic;
                }
            
                if ($request->has('profile_status')) {
                    $profileUpdateData['status'] = $request->profile_status;
                }

                // Update first_name and last_name in the profiles table
                $updateprofile = Profiles::where('user_id', $request->edit)->update($profileUpdateData);

                if (is_int($request->role)) {
                    $request->role = [$request->role];
                }

                if ($request->has('role')) {
                    UserRoles::where('user_id', $request->edit)->delete();
                    foreach ($request->role as $role) {
                        $userrole = new UserRoles();
                        $userrole->user_id = $request->edit;
                        $userrole->role_id = $role['value'];

                        if (!$userrole->save()) {
                            $issaved = false;
                        }
                    }
                }

                if ($updateuser && $updateprofile) {
                    $issaved = true;
                }

            } else {
                $user = new User();
                $user->username = $request->username;
                $user->email = $request->email;
                $user->password = bcrypt($request->password ?? env('DEFAULT_PASSWORD'));
                $user->created_by = env('USER_ID', 1);
                $user->updated_by = env('USER_ID', 1);
                $user->save();

                $profile = new Profiles();
                $profile->user_id = $user->user_id;
                $profile->first_name = $request->first_name;
                $profile->last_name = $request->last_name;
                $profile->supervisor_id = $request->supervisor;
                $profile->profile_pic_id = $request->profile_pic;
                $profile->created_by = env('USER_ID', 1);
                $profile->updated_by = env('USER_ID', 1);
                $profile->save();

                if (is_int($request->role)) {
                    $request->role = [$request->role];
                }

                if ($request->has('role')) {
                    foreach ($request->role as $role) {
                        $userrole = new UserRoles();
                        $userrole->user_id = $user->user_id;
                        $userrole->role_id = $role['value'];
                        $userrole->save();

                        if (!$userrole->save()) {
                            $issaved = false;
                        }
                    }
                }

                if ($user->save() && $profile->save()) {
                    $issaved = true;
                }
            }
            DB::commit();
            return $issaved;
        } catch (\Exception $ex) {
            // DB::rollBack();
            throw new \Exception('Error in creating/updating the user: ' . $ex->getMessage());
        }
    }
    
    public function manageUserService($userid = null, $supervisor = null)
    {
        $supervisorRoleId = env('SUPERVISOR_ID');
        $users = User::query();

        if ($userid) { // fetching a particular user
            $users->where('users.user_id', $userid);
        }

        // ->leftJoin('profiles as createdby_profile', 'users.created_by', '=', 'createdby_profile.user_id')
        // ->leftJoin('profiles as updatedby_profile', 'users.updated_by', '=', 'updatedby_profile.user_id')
        $users->leftJoin('profiles', 'users.user_id', '=', 'profiles.user_id')
        ->leftJoin('users_roles', 'users.user_id', '=', 'users_roles.user_id')
        ->leftJoin('roles', 'users_roles.role_id', '=', 'roles.role_id')
        ->select(
            'users.*',
            'profiles.*',
            // DB::raw('STRING_AGG(roles.role_id::text, \', \') AS role_ids'),
            // DB::raw('STRING_AGG(roles.title::text, \', \') AS role_names'),
            DB::raw('JSON_AGG(roles) AS roles')

            // 'createdby_profile.first_name as created_by_first_name',
            // 'createdby_profile.last_name as created_by_last_name',
            // 'updatedby_profile.first_name as updated_by_first_name',
            // 'updatedby_profile.last_name as updated_by_last_name'
        )
        ->groupBy('users.user_id', 'profiles.profile_id')
        ->orderBy('users.created_at', 'desc');

        $data = $users->get();
        // Convert the roles column from JSON string to array of objects
        foreach ($data as $item) {
            $item->roles = json_decode($item->roles);
        }

        if ($supervisor) {
            $supervisorUsers = collect($data)->filter(function ($item) use ($supervisorRoleId) {
                return $item->roles && collect($item->roles)->contains('role_id', $supervisorRoleId);
            })->toArray(); // Supervisor users
            
            return [
                'supervisor_users' => $supervisorUsers,
                'all_users' => $data->toArray()
            ];
        }

        return $data;
    }
}
