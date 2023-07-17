<?php

namespace App\Http\Controllers\Categories;


use Illuminate\Http\Request;
use App\Http\Controllers\Categories\Services\CategoriesClass;
use App\Http\Controllers\Controller;

class CategoriesController extends Controller {

    protected $serviceClass;

    public function __construct() 
    {
        $this->serviceClass = new CategoriesClass();
    }

    public function createCategory(Request $request, $editid = null) 
    {
        if ($editid != null){
            $categoryDetails = $this->serviceClass->getCategoryDetails($editid);
            return view('categories.categories', compact('categoryDetails', 'editid'));
        }
        return view('categories.categories');
        
    }

    public function storeCategory(Request $request) 
    {

        $request->validate([
            'title' => 'required|unique:categories|max:50',
        ]);

        $result = $this->serviceClass->saveCategory($request);
        if($result === true) {
            return redirect("/categories/manage")->withSuccess('Category created successfully');
        } else {
            return redirect()->back()->withError('Something went wrong while creating category');
        }
    }


    public function updateCategory(Request $request, $editid = null) 
    {   
        $request->validate([
            'title' => 'required|max:50|unique:categories,title,'.$editid.',category_id'
        ]);

        $result = $this->serviceClass->editCategory($request, $editid);

        if($result === true) {
            return redirect("/categories/manage")->withSuccess('Category updated successfully');
        } else {
            return redirect()->back()->withError('Something went wrong while updating category');

        }    

    }

    public function updateStatus(Request $request, $editid = null) 
    {   
        $result = $this->serviceClass->changeStatus($request, $editid);

        if($result === true) {
            return redirect()->back()->withSuccess('Status updated successfully');
        } else {
            return redirect()->back()->withError('Something went wrong while updating status');

        }    }
}

?>