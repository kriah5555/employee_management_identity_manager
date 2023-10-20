<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Traits\HasPermissions;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, LogsActivity, HasRoles, HasPermissions;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        // 'email',
        'status',
        'created_by',
        'updated_by'
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['username', 'password', 'status'])
            ->logOnlyDirty(['username', 'password', 'status'])
            ->dontSubmitEmptyLogs();
    }

    public function isActive(): bool
    {
        return $this->status;
    }

    public function findForPassport($username)
    {
        return $this->where('username', $username)->first();
    }

    public function isSuperadmin(): bool
    {
        return $this->is_superadmin;
    }

    public function isStaff(): bool
    {
        return $this->is_staff;
    }

    public function checkUserNameExist($username)
    {
        return $this->where('username', $username)->first();
    }

    // public function validateForPassportPasswordGrant($password)
    // {
    //     return Hash::check($password, $this->password);
    // }

    public function conversations()
    {
        return $this->belongsToMany(Conversation::class, 'conversation_users', 'user_id', 'conversation_id');
    }
}
