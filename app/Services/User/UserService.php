<?php

namespace App\Services\User;

use Illuminate\Http\Request;
use App\Http\Rules\{CreateEditEmployee, CreateUserRequest};
use App\Models\{
    Countries,
    CountryNationality,
    Gender,
    Languages,
    MaritalStatus,
    User,
    UserBasicDetails,
    UserPersonalDetails,
    UserAddressDetails,
    InviteUserTokens
};
use Symfony\Component\Uid\Ulid;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use Illuminate\Support\Facades\DB;

use function PHPSTORM_META\type;

class UserService
{

    protected $country;
    protected $nationality;
    protected $ulid;
    protected $userObject;
    protected $basic;
    protected $personal;
    protected $invite;


    public function __construct()
    {
        $this->country = new Countries();
        $this->nationality = new CountryNationality();
        $this->ulid = new Ulid();
        $this->userObject = new User();
        $this->basic = new UserBasicDetails();
        $this->personal = new UserPersonalDetails();
        $this->invite = new InviteUserTokens();

    }

    public static function formatCountryAndNationality($raw)
    {
        $data = [];
        foreach ($raw as $value) {
            $data['country'][] = [
                'id'    => $value['id'],
                'value' => $value['name'],
                'code'  => $value['iso_code_2']
            ];
            $data['nationality'][] = [
                'id'         => $value['nationality']['id'],
                'value'      => $value['nationality']['nationality'],
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

    public function createUserName($userName, $flag = null)
    {

        if ($this->userObject->checkUserNameExist($userName . $flag) == null) {
            return $userName . $flag;
        } else {
            return $this->createUserName($userName, $flag + 1);
        }
    }

    public function createEmployeeService($employee_details)
    {
        $userName = str_replace(' ', '', $employee_details['first_name'] . $employee_details['last_name']);
        $password = str_replace(' ', '', $employee_details['first_name'] . date('dmY', strtotime($employee_details['birth_date'])));
        $userName = $this->createUserName($userName);

        $newUser = $this->userObject->create(
            [
                'username' => $userName,
                'email'    => $employee_details['email'],
                'password' => bcrypt($password),
            ]
        );

        $userId = $newUser->id;
        $this->basic->create(
            [
                'user_id'        => $userId,
                'first_name'     => $employee_details['first_name'],
                'last_name'      => $employee_details['first_name'],
                'email'          => $employee_details['email'],
                'mobile'         => $employee_details['mobile'],
                'rsz_number'     => $employee_details['rsz_number'],
                'birth_date'     => $employee_details['birth_date'],
                'birth_place'    => $employee_details['birth_place'],
                'bank_account'   => $employee_details['bank_number'],
                'gender_id'      => $employee_details['gender_id'],
                'nationality_id' => $employee_details['nationality_id'],
                'language_id'    => $employee_details['language_id'],
                'created_by'     => $employee_details['current_user_id'],
            ]
        );
        $this->personal->create([
            'user_id'             => $userId,
            'marital_status_id'   => $employee_details['marital_status'],
            'dependent_spouse_id' => $employee_details['dependent_spouse'],
            'dependent_children'  => $employee_details['childrens_count'],
        ]);
    }

    public function generateULID()
    {
        return Str::ulid()->toBase58();
    }

    public function inviteEmployee($invitation)
    {

        $this->invite->updateOrInsert(['mail' => $invitation['mail']], [
            'mail'        => $invitation['mail'],
            'token'       => $this->generateULID(),
            'expire_at'   => date('Y-m-d H:i:00', strtotime('+1 day')),
            'invite_role' => $invitation['invite_role'],
            'invite_by'   => $invitation['invite_by'],
            'company_id'  => $invitation['company_id'],
        ]);

        //we have trigger mail funciton from here.
    }

    public function forgotPassword($request)
    {
        try {
            $email = $request['email']; // Get the email from the request
            $username = $request['username'];

            // Check if there's a matching record in the database
            $matchingUser = DB::table('users')->where('email', $email)->where('username', $username)->first();

            if (!$matchingUser) {
                return response()->json(['status' => false, 'message' => "Email and username do not match"], 400);
            }

            // Delete all old codes that the user sent before.
            ResetCodePassword::where('email', $email)->delete();

            // Generate a random code
            $code = mt_rand(100000, 999999);

            // Create a new code with the current timestamp
            ResetCodePassword::create([
                'email' => $email,
                'code' => $code,
                'created_at' => now(), // Manually set the created_at attribute
            ]);

            // Send an email to the user
            Mail::to($email)->send(new SendCodeResetPassword($code));

            return response()->json(['status' => true, 'message' => "OTP has been sent to the email id."], 200);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }



    public function resetPassword($request)
    {
        try {
            $passwordReset = ResetCodePassword::firstWhere('code', $request['otp']);

            // Check if it has expired: the time is one hour
            if ($passwordReset->created_at > now()->addMinutes(15)) {
                $passwordReset->delete();
                return response(['message' => trans('passwords.code_is_expire')], 422);
            }

            // If the code is valid and not expired, update the user's password
            $user = User::where('email', $passwordReset->email)->first();
            $newPassword = $request['new_password'];
            $confirmPassword = $request['confirm_new_password'];

            // Check if new_password and confirm_password match
            if ($newPassword === $confirmPassword) {
                // Hash and save the new password
                $user->password = Hash::make($newPassword);
                $user->save();

                // Delete the used reset code
                $passwordReset->delete();

                return response()->json([
                    'success' => true,
                    // 'message' => [
                    //     'code' => $passwordReset->code,
                    //     'message' => trans('passwords.code_is_valid'),
                    //     'updated_password' => $newPassword,
                    // ]
                    'message' => 'password updated successfully'
                ], 200);

            } else {

            return response()->json(['status' => false, 'message' => 'New password and confirmation do not match.'], 400);

            }
        } catch (\Exception $e) {
            return response(['message' => 'An error occurred while resetting the password.'], 500);
        }
    }

    public function getDependentSpouseOptions()
    {
        return config('constants.DEPENDENT_SPOUSE_OPTIONS');
    }

    public function getLanguageOptions()
    {
        return config('constants.LANGUAGE_OPTIONS');
    }

    public function updateEmployee($request)
    {
        dd($request);

    }


}
