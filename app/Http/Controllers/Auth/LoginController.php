<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Sanctum\Sanctum;
use App\Models\Users\Profiles;
use App\Http\Controllers\EncryptDecryptController;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $credentials = $request->only('username', 'password');

            // Check if the input is an email address
            if (filter_var($credentials['username'], FILTER_VALIDATE_EMAIL)) {
                // If it is an email, use it as the 'email' field in the credentials array
                $credentials['email'] = $credentials['username'];
                unset($credentials['username']);
            }

            if (Auth::attempt($credentials)) { // checking the auth credentials
                $user = Auth::user();
                if ($user->status) {
                    $token = $user->createToken('AuthToken')->plainTextToken;

                    $profile = Profiles::where('user_id', $user->user_id)->first();

                    $data = [ // constructing the data to be sent to the frontend
                        'user_id' => $user->user_id,
                        'name' => $profile->first_name ." ". $profile->last_name,
                        'email' => $profile->email,
                        'username' => $user->username,
                        'token' => $token,
                    ];

                    // encrypting and constructing the redirect url
                    $redirectUrl = env('RENEWAL_APP_URL').'?'
                        . http_build_query(['token' => EncryptDecryptController::encryptcode(json_encode($data))]);

                    return redirect()->away($redirectUrl);
                }
                return redirect("/")->withError('Account has been deactivated');
            } else {
                return redirect("/")->withError('Incorrect username or password');
            }
        } catch (\Exception $e) {
            return redirect("/")->withError('An error occurred. Please try again.');
        }
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        return response()->json(['message' => 'Successfully logged out'], 200);
    }
}
