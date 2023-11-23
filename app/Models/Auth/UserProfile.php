<?php

namespace App\Models\Users;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profiles extends Model
{
    use HasFactory;

    protected $table = 'profiles';

    /**
     * The primary key for the table.
     *
     * @var string
     */
    protected $primaryKey = 'profile_id';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'email',
        'supervisor_id',
        'profile_pic_id',
        'status',
        'created_by',
        'updated_by'
    ];
}