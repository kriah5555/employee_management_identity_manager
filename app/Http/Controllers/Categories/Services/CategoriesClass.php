<?php

namespace App\Http\Controllers\Categories\Services;

use App\Http\Controllers\Categories\Services\CategoriesService;
use App\Models\Categories;
use App\Models\Permissions;
use App\Models\RolesPermissions;

class CategoriesClass 
{
    private $service = null;

    public function __construct()
    {
        $this->service = new CategoriesService();
    }

    public function saveCategory($request)
    {
        $responseData = false;
        $data = $request->all();
        $response = $this->service->ConstructCategoryObj($data);
        try {
            $status = Categories::create($response);
            $responseData = true;
        } catch (\Throwable $th) {
            // throw $th;
        }
        return $responseData;
    }

    public function getCategoryDetails($editid)
    {
        $categoryDetails = Categories::where('category_id', $editid)->get();
        return $categoryDetails;
    }

    public function editCategory($request, $editid)
    {
        $responseData = false;
        $data = $request->all();
        try {
            $response = Categories::where('category_id', $editid)
                ->update([
                    'title' => $data['title'],
                    'updated_by' => env('USER_ID', 1)    
                ]);
            $responseData = true;
        } catch (\Throwable $th) {
            // return response()->json(['error' => 'Something went wrong'], 500);
        }
        return $responseData;
    }

    public function changeStatus($request, $editid)
    {
        $responseData = false;
        $status = Categories::find($editid);
        try {
            $permissionsArray = Permissions::where('category_id', $editid)->pluck('permission_id')->toArray();
            $response = Categories::where('category_id', $editid)
                ->update([
                    'status' => !$status->status,
                    'updated_by' => env('USER_ID', 1)
                ]);
            $permissions = Permissions::where('category_id', $editid)
                ->update([
                    'status' => !$status->status,
                    'updated_by' => env('USER_ID', 1)
                ]);
                
            if ($status->status === true) {
                $roleSPermissions = RolesPermissions::whereIn('permission_id',$permissionsArray)
                    ->update([
                        'status' => !$status->status,
                        'updated_by' => env('USER_ID', 1)
                    ]);
            }
            $responseData = true;
        } catch (\Throwable $th) {
            throw $th;
            // return response()->json(['error' => 'Something went wrong'], 500);
        }
        return $responseData;
    }

}