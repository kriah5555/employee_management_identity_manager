<?php

namespace App\Services;

// use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
// use App\Models\User;
use App\Models\Users\Profiles;
use App\Models\Users\UserRoles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
// use App\Helpers;
use App\Models\{Countries, CountryNationality, Gender, Languages, MaritalStatus, User};

class UserService
{

    protected $country;
    protected $nationality;

    public function __construct()
    {
        $this->country = new Countries();
        $this->nationality = new CountryNationality();
    }

    public function createUser($values)
    {
        try {
            DB::beginTransaction();
            $values['username'] = generateUniqueUsername($values['username']);
            $values['password'] = Hash::make(config('auth.default_user_password'));
            $user = User::create($values);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function updateUser()
    {
        $profileUpdateData = [];
        $userData = [];
        if ($request->has('username')) {
            $userData['username'] = $request->username;
        }
    
        if ($request->has('user_status')) {
            $userData['status'] = $request->user_status;
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
    }
    
    public function manageUserService($userid = null, $supervisor = null)
    {
        $supervisorRoleId = env('SUPERVISOR_ID');
        $users = User::query();

        if ($userid) { // fetching a particular user
            $users->where('users.user_id', $userid);
        }
        $users->leftJoin('profiles', 'users.user_id', '=', 'profiles.user_id')
        ->leftJoin('users_roles', 'users.user_id', '=', 'users_roles.user_id')
        ->leftJoin('roles', 'users_roles.role_id', '=', 'roles.role_id')
        ->select(
            'users.*',
            'profiles.*',
            DB::raw('JSON_AGG(roles) AS roles')
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


    public static function formatCountryAndNationality($raw)
    {
        $data = [];
        foreach($raw as $value) {
            $data['country'][] = [
                'id' => $value['id'],
                'value' => $value['name'],
                'code' => $value['iso_code_2']
            ];
            $data['nationality'][] = [
                'id' => $value['nationality']['id'],
                'value' => $value['nationality']['nationality'],
                'country_id' => $value['nationality']['country_id']
            ];
        }
        return $data;
    }


    public static function getEmployeeOptionsService()
    {
        //Countries, CountryNationality, Gender, Languages, MaritalStatus, User
        $data = [];
        $countries = Countries::with('nationality')->get()->toArray();
        $data = self::formatCountryAndNationality($countries);

        $data['gender'] = Gender::getGenders();
        
        $data['languages'] = Languages::getLanguages();
        $data['marital_status'] = MaritalStatus::getMaritalStatus();

        $data['dependent_spouse'] = ['yes' => 'Yes', 'no' => 'No'];
        return $data;
    }

    public function removeExtraColumns($data)
    {

    }
}
