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


    public function getDependentSpouseOptions()
    {
        return config('constants.DEPENDENT_SPOUSE_OPTIONS');
    }

    public function getLanguageOptions()
    {
        return config('constants.LANGUAGE_OPTIONS');
    }
}