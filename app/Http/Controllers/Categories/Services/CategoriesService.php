<?php

namespace App\Http\Controllers\Categories\Services;

class CategoriesService
{
    public function ConstructCategoryObj($data)
    {
        $category = [];
        $category['title'] = $data['title'];
        $category['created_by'] = env('USER_ID', 1);
        $category['updated_by'] = env('USER_ID', 1);

        return $category;
    }
}