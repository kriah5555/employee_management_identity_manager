<?php

namespace App\Http\Controllers\Manage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Manage\Services\ManagePageClass;


class ManagePageController extends Controller
{
    private $serviceClass;

    public function __construct()
    {
        $this->serviceClass = new ManagePageClass();
    }

    public function manage(Request $request)
    {
        $pk_id = '';
        $pageType = $request->type;
        $pageDetails = $this->serviceClass->getPageDetails($request);
        $categoryDetails = $this->serviceClass->getCategoryDetails();
        
        if ($pageType === 'categories') {
            $pk_id = 'category_id';
        } elseif ($pageType === 'permissions') {
            $pk_id = 'permission_id';
        } elseif ($pageType === 'roles') {
            $pk_id = 'role_id';
        }

        return view('manage.manage-page', compact('pageDetails','pageType', 'pk_id', 'categoryDetails'));
    }


    public function seachField(Request $request)
    {
        return $request;
        $pageType = $type;
        $pageDetails = $this->serviceClass->getPageDetails($type);
        $tableHeading = array_keys($pageDetails[0]);
        return view('manage.manage-page', compact('tableHeading','pageDetails', 'pageType'));
    }

}
