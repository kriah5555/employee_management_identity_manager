<?php

namespace App\Http\Controllers\Permissions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;

class PermissionsController extends Controller
{

    public function __construct()
    {
    }

    public function testing()
    {
        $role = Role::where('name', 'admin')->first();
        print_r($role);
        exit;
    }
}