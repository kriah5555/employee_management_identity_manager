<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResetCodePassword extends Model
{
    use HasFactory;
    protected $fillable = [
        'email',
        'code',
        'created_at',
    ];
    public $timestamps = false;


    public function isExpire()
    {
        if ($this->created_at->addMinutes(10) < now()) {
            $this->delete();
        }
    }



}
