<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    use HasFactory;
    protected $table =  'roles';
    protected $primaryKey = 'role_id';
    protected $fillable = [
        'title',
        'status',
        'type',
        'created_by',
        'updated_by',
    ];

}
