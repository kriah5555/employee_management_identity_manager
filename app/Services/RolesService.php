<?php

namespace App\Http\Controllers\Roles\Services;

use App\Http\Controllers\Roles\Services\RolesService;
use App\Models\Permissions;
use App\Models\Roles;
use App\Models\RolesPermissions;
use App\Models\Categories;
use DB;
use Carbon\Carbon;

class RolesService
{
    protected $service = null;

    public function __construct()
    {
        $this->service = new RolesService();
    }

    public function RolesService($request)
    {
        $responseData = [];
        $data = '';
        // print_r($request->all());exit;

        $roleData = array_filter([
            'title'      => $request->title,
            'status'     => $request->status,
            'created_by' => env('USER_ID', 1),
            'updated_by' => env('USER_ID', 1)
        ], function ($value) {
            return !is_null($value);
        });

        try {
            DB::beginTransaction();

            if ($request->edit && $request->permissions) {
                $data = Roles::find($request->edit);
                $data = $data->update($roleData);
                RolesPermissions::where('role_id', $request->edit)->delete();

            } elseif ($request->edit && $request->status) {
                $data = Roles::find($request->edit);
                $data = $data->update(['status' => $request->status === 'true' ? true : false]);
            } else {
                $role_id = Roles::create($roleData)->role_id;
            }

            if (!$request->status) {
                $permissionObject = $this->constructPermissionObject($request->edit ? $request->edit : $role_id, $request);
                $result = RolesPermissions::insert($permissionObject);
            }

            DB::commit();

            $message = $request->edit ? 'edited' : 'created';

            $responseData = response()->json([
                'status'  => 200,
                'message' => 'role ' . $message . ' successfully',
            ], 200);

        } catch (\Throwable $th) {
            DB::rollback();
            // throw $th;
            $responseData = response()->json([
                'status'  => 500,
                'message' => 'Something went wrong',
            ], 500);
        }
        return $responseData;
    }


    public function getOrManageRoles($request)
    {
        $responseData = [];
        $role_name = '';
        $editid = $request->id;
        try {
            $roleDetails = DB::table('roles')->select('roles.*');
            $rolesPermissionsCount = RolesPermissions::where('role_id', '=', $editid)->where('status', '=', true)->count();
            if ($editid) {
                if ($rolesPermissionsCount === 0) {
                    $role_name = Roles::where('role_id', '=', $editid)->pluck('title');
                }
                $roleDetails->where('roles.role_id', $editid)
                    ->leftJoin('roles_permissions', 'roles_permissions.role_id', '=', 'roles.role_id');
                $roleDetails->where('roles_permissions.status', '=', true);
                $roleDetails->leftJoin('permissions', 'permissions.permission_id', '=', 'roles_permissions.permission_id')
                    ->select(
                        'roles_permissions.*',
                        'roles.title as role_name',
                        'permissions.*',
                    );
            }

            $roleDetails = $roleDetails->get();

            $responseData = response()->json([
                'status'    => 200,
                'message'   => 'data fetched successfully',
                'role_name' => $role_name,
                'data'      => $roleDetails
            ]);
        } catch (\Throwable $th) {
            throw $th;
            $responseData = response()->json([
                'status'  => 500,
                'message' => 'something went wrong'
            ]);
        }
        return $responseData;
    }
    public function constructPermissionObject($role_id, $request)
    {
        $data = $request->all();
        $permissions = $data['permissions'];
        $permissionObject = [];
        $currentDateTime = Carbon::now()->toDateTimeString();

        foreach ($permissions as $key => $value) {
            $permissionObject[$key]['role_id'] = $role_id;
            $permissionObject[$key]['permission_id'] = $value;
            $permissionObject[$key]['created_by'] = env('USER_ID', 1);
            $permissionObject[$key]['updated_by'] = env('USER_ID', 1);
            $permissionObject[$key]['created_at'] = $currentDateTime;
            $permissionObject[$key]['updated_at'] = $currentDateTime;
        }
        return $permissionObject;
    }
}