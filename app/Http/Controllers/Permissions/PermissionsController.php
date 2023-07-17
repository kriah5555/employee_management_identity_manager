<?php

namespace App\Http\Controllers\Permissions;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Permissions\Services\PermissionsClass;

class PermissionsController extends Controller
{
    private $serviceClass;

    public function __construct()
    {
        $this->serviceClass = new PermissionsClass();
    }

    public function createPermission(Request $request, $editid = null)
    {
        $categoriesList = $this->serviceClass->getCategories();
        if ($editid != null){
            $permissionDetails = $this->serviceClass->getPermissionDetails($editid);
            return view('permissions.permissions', compact('categoriesList','permissionDetails', 'editid'));
        }
        return view('permissions.permissions',compact('categoriesList'));
    }

    public function storePermission(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:permissions|max:50',
            'category' => 'required',
        ]);

        $result = $this->serviceClass->savePermission($request);
        if($result === true) {
            return redirect("/permissions/manage")->withSuccess('Permission created successfully');
        } else {
            return redirect()->back()->withError('Something went wrong while creating permission');
        }
    }


    public function updatePermission(Request $request, $editid = null)
    {
        $request->validate([
            'title' => 'required|max:50|unique:permissions,title,'.$editid.',permission_id',
            'category' => 'required',
        ]);
        $result = $this->serviceClass->editPermissions($request, $editid);
        if($result === true) {
            return redirect("/permissions/manage")->withSuccess('Permission updated successfully');
        } else {
            return redirect()->back()->withError('Something went wrong while updating permission');
        }    
    }

    public function getPermissions($editid = null)
    {
        return $this->serviceClass->fetchPermissions($editid);
    }

    public function updateStatus($editid = null)
    {
        $result = $this->serviceClass->changeStatus($editid);
        if($result === true) {
            return redirect()->back()->withSuccess('Status updated successfully');
        } else {
            return redirect()->back()->withError('Something went wrong while updating status');
        }

    }
}
