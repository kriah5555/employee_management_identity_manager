<?php

namespace App\Http\Controllers\Roles;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Services\RolesService;

class RolesController extends Controller
{
    protected $serviceClass = null;

    public function __construct()
    {
        $this->serviceClass = new RolesService();
    }

    public function storeRole(Request $request)
    {
        return $this->serviceClass->saveRoles($request);
    }

    public function manageRole(Request $request)
    {
        return $this->serviceClass->getOrManageRoles($request);
    }
}
