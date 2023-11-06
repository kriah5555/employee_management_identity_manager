<?php

namespace App\Http\Controllers\Permissions\Services;

use App\Http\Controllers\Permissions\Services\PermissionsService;
use App\Models\Permissions;
use App\Models\Categories;
use App\Models\RolesPermissions;
use DB;

class PermissionsClass
{
    private $service = null;

    public function __construct()
    {
        $this->service = new PermissionsService();
    }

    public function getCategories()
    {
        try {
            $categoriesQuery = Categories::where('status', true);
            $categoriesList = $categoriesQuery->get();
            return $categoriesList;
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function getPermissionDetails($editid)
    {
        try {
            $permissionDetails = Permissions::where('permission_id', $editid)->get();
            return $permissionDetails;
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }

    public function savePermission($request)
    {
        $responseData = false;
        $data = $request->all();
        $response = $this->service->ConstructPermissionObj($data);
        try {
            $result = Permissions::create($response);
            $responseData = true;
        } catch (\Throwable $th) {
            // throw $th;
            // return response()->json(['error' => 'Something went wrong'], 500);
        }
        return $responseData;
    }

    public function editPermissions($request, $editid = null)
    {
        $responseData = false;
        $data = $request->all();
        $response = $this->service->ConstructPermissionObj($data);
        try {
            $result = Permissions::where('permission_id', $editid)->update($response);
            $responseData = true;
        } catch (\Throwable $th) {
            //throw $th;
            // return response()->json(['error' => 'Something went wrong'], 500);
        }
        return $responseData;
    }

    public function changeStatus($editid = null)
    {
        $responseData = false;
        $status = Permissions::find($editid);
        try {
            $result = Permissions::where('permission_id', $editid)->update([
                'status'     => !$status->status,
                'updated_by' => env('USER_ID', 1)
            ]);

            if ($status->status === true) {
                // RolesPermissions::where('permission_id', $editid)->delete();

                $roleSPermissions = RolesPermissions::where('permission_id', $editid)
                    ->update([
                        'status'     => !$status->status,
                        'updated_by' => env('USER_ID', 1)
                    ]);
            }
            $responseData = true;
        } catch (\Throwable $th) {
            //throw $th;
            // return response()->json(['error' => 'Something went wrong'], 500);
        }
        return $responseData;
    }

    public function fetchPermissions($editid)
    {
        $responseData = [];

        try {
            $permissionDetails = DB::table('categories as c')
                ->select(
                    'c.category_id as cid',
                    'c.title as category_name',
                    'permissions.*',
                );
            $permissionDetails->leftjoin('permissions', 'permissions.category_id', '=', 'c.category_id');

            if ($editid) {
                $permissionDetails->where('permissions.permission_id', $editid);
                // ->where('permissions.status','=',true);
            }
            $permissionDetails->where('permissions.status', '=', true);
            $permissionDetails->where('c.status', '=', true);
            $permissionDetails = $permissionDetails->get()->groupBy('cid');

            // foreach($permissionDetails as $key => $val){
            //     foreach ($val as $key2 => $value2) {
            //         $permissionDetails[$key][$key2]->checked = false;
            //         # code...
            //     }
            // }

            $responseData = response()->json([
                'status'  => 200,
                'message' => 'data fetched successfully',
                'data'    => $permissionDetails
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
}