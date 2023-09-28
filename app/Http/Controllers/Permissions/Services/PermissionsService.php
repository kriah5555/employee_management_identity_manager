<?php

namespace App\Http\Controllers\Permissions\Services;

class PermissionsService
{
    public function ConstructPermissionObj($data)
    {
        $permission = [];
        $permission['title'] = $data['title'];
        $permission['category_id'] = $data['category'];
        $permission['created_by'] = env('USER_ID', 1);
        $permission['updated_by'] = env('USER_ID', 1);
        return $permission;
    }
}