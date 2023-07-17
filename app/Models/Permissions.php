<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    use HasFactory;
    protected $table =  'permissions';
    protected $primaryKey = 'permission_id';
    protected $fillable = [
        'title',
        'category_id',
        'status',
        'created_by',
        'updated_by',
    ];
}
