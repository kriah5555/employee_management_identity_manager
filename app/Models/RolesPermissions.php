<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolesPermissions extends Model
{
    use HasFactory;
    protected $table = 'roles_permissions';
    protected $primaryKey = 'role_id';
    protected $fillable = [
        'permission_id',
        'status',
        'created_by',
        'updated_by',
    ];
}