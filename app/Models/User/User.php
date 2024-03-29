<?php

namespace App\Models\User;

use App\Models\DeviceToken;
use Laravel\Passport\HasApiTokens;
use App\Models\User\UserBankAccount;
use App\Models\User\UserBasicDetails;
use App\Models\User\UserFamilyDetails;
use Spatie\Permission\Traits\HasRoles;
use App\Models\User\UserProfilePicture;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasPermissions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, HasRoles, HasPermissions;
    protected $guard_name = 'api';

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'social_security_number',
        'password',
        'status',
        'is_admin',
        'is_moderator'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected static function booted()
    {
        static::addGlobalScope('sort', function ($query) {
            $query->orderBy('username', 'asc');
        });
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }
    public function userBasicDetails()
    {
        return $this->hasOne(UserBasicDetails::class);
    }
    public function userContactDetails()
    {
        return $this->hasOne(UserContactDetails::class);
    }
    public function userAddress()
    {
        return $this->hasOne(UserAddress::class);
    }
    public function userBankAccount()
    {
        return $this->hasOne(UserBankAccount::class);
    }
    public function userFamilyDetails()
    {
        return $this->hasOne(UserFamilyDetails::class);
    }

    // public function validateForPassportPasswordGrant($password)
    // {
    //     return Hash::check($password, $this->password);
    // }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_users', 'user_id', 'conversation_id');
    }

    public function deviceToken()
    {
        return $this->hasOne(DeviceToken::class);
    }

    public function userProfilePicture()
    {
        return $this->hasOne(UserProfilePicture::class);
    }
}
