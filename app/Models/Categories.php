<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categories extends Model
{
    use HasFactory;
    protected $table =  'categories';
    protected $primaryKey = 'category_id';
    protected $fillable = [
        'title',
        'status',
        'type',
        'created_by',
        'updated_by',
    ];

}
