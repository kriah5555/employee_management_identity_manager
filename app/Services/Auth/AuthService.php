<?php

namespace App\Services\Auth;

use App\Models\Auth\Permission;
use App\Models\User\CompanyUser;
use App\Models\User\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Client as OClient;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\AuthenticationException;
use App\Models\Auth\Role;


class AuthService
{
    public function createUser($values)
    {
        try {
            DB::beginTransaction();
            $values['username'] = generateUniqueUsername($values['username']);
            $password = array_key_exists('password', $values) ? $values['password'] : config('auth.default_user_password');
            $values['password'] = Hash::make($password);
            $user = User::create($values);
            DB::commit();
            return $user;
        } catch (\Exception $e) {
            DB::rollBack();
            throw new \Exception($e->getMessage());
        }
    }

    public function validateUserCredentials(array $credentials)
    {
        $username = $credentials['username'] ?? null;
        $password = $credentials['password'] ?? null;

        if (Auth::attempt(['username' => $username, 'password' => $password])) {
            return Auth::user();
        }

        return null;
    }

    public function generateUserTokens(array $credentials)
    {
        try {

            $oClient = $this->getOAuthClient();

            $response = microserviceRequest(
                '/service/identity-manager/oauth/token',
                'POST',
                array_merge($credentials, [
                    'grant_type'    => 'password',
                    'client_id'     => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'scope'         => '*',
                ])
            );

            if ($response->getStatusCode() != 200) {
                throw new \Exception(data_get($response->json(), 'message', 'Error in generating token'));
            }

            return $response->json();

        } catch (\Exception $e) {
            throw $e;
        }
    }



    protected function handleDeviceToken($user, $deviceTokenValue)
    {
        // Check if the device token is already associated with another user
        $existingUserWithToken = \App\Models\DeviceToken::where('device_token', $deviceTokenValue)
            ->where('user_id', '!=', $user->id)
            ->first();

        if ($existingUserWithToken) {

            $existingUserWithToken->update(['user_id' => $user->id]);

        }

        // Check if the combination of user_id and device_token already exists
        $existingDeviceToken = $user->deviceToken()->where('device_token', $deviceTokenValue)->first();

        // If the combination already exists, update the device_token and continue
        if ($existingDeviceToken) {
            $existingDeviceToken->update(['device_token' => $deviceTokenValue]);
        } else {
            // Generate a unique identifier for the device-token-user combination
            $uniqueIdentifier = hash('sha256', $deviceTokenValue . $user->id);

            // Associate the new device token and unique identifier with the user
            $user->deviceToken()->create([
                'device_token'      => $deviceTokenValue,
                'unique_identifier' => $uniqueIdentifier,
            ]);
        }
    }



    public function getOAuthClient()
    {
        try {
            return OClient::where('password_client', 1)->firstOrFail();
        } catch (\Exception $e) {
            throw new \Exception("OAuth client not found.");
        }
    }

    public function refreshUserTokens(array $credentials)
    {
        $oClient = $this->getOAuthClient();

        $response = microserviceRequest(
            '/service/identity-manager/oauth/token',
            'POST',
            array_merge($credentials, [
                'grant_type'    => 'refresh_token',
                'client_id'     => $oClient->id,
                'client_secret' => $oClient->secret,
                'scope'         => '*',
            ])
        );

        if ($response->getStatusCode() == 401) {
            throw new AuthenticationException(data_get($response->json(), 'message', 'Error in generating token'));
        } elseif ($response->getStatusCode() != 200) {
            throw new \Exception(data_get($response->json(), 'message', 'Error in generating token'));
        }

        return $response->json();
    }

    public function setActiveUserByUid($uid)
    {
        $user = User::findOrFail($uid);
        if ($user) {
            // Log in the user
            Auth::login($user);
        }
    }

    public function mobileLogin($values)
    {
        $user = $this->validateUserCredentials($values);
        if ($user) {
            if (isset($values['device_token'])) {
                $this->handleDeviceToken($user, $values['device_token']);
            }
            return $this->getMobileLoginResponse($user);
        } else {
            throw new AuthenticationException("The user credentials were incorrect.");
        }
    }

    public function webLogin($values)
    {
        $user = $this->validateUserCredentials($values);
        if ($user) {
            if (isset($values['device_token'])) {
                $this->handleDeviceToken($user, $values['device_token']);
            }
            return $this->getMobileLoginResponse($user);
        } else {
            throw new AuthenticationException("The user credentials were incorrect.");
        }
    }

    public function getMobileLoginResponse($user)
    {
        $managerFlow = 0;
        $employeeFlow = 0;
        if ($user->is_admin || $user->is_moderator) {
            $managerFlow = 1;
        }
        $companyUsers = CompanyUser::where('user_id', $user->id)->get();
        $employeeRole = Role::where('name', 'employee')->first();
        $webAccessPermission = Permission::where('name', 'Web app access')->first();
        foreach ($companyUsers as $companyUser) {
            if ($companyUser->hasPermissionTo($webAccessPermission)) {
                $managerFlow = 1;
            }
            if ($companyUser->hasRole($employeeRole)) {
                $employeeFlow = 1;
            }
        }
        return [
            'uid'      => $user->id,
            'username' => $user->username,
            'name'     => $user->userBasicDetails->first_name . ' ' . $user->userBasicDetails->last_name,
            'access'   => [
                'manager_flow'  => $managerFlow,
                'employee_flow' => $employeeFlow,
            ]
        ];
    }

}
