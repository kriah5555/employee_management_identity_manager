<?php

namespace App\Http\Controllers\Manage\Services;

use App\Models\Categories;
use App\Models\Permissions;
use App\Models\Roles;
use DB;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;

class ManagePageClass
{
    public function __construct()
    {

    }

    public function getPageDetails($request)
    {
        $pageType = $request->type;
        try {
            $pageDetails = DB::table($pageType);
            if ($request->has('search') && $request->get('search') != null) {
                $pageDetails = $pageDetails->where('title', 'ILIKE', "%{$request->input('search')}%");
            }
            $pageDetails = $pageDetails->orderBy('created_at', 'asc')->get();
            $activeRecords = $pageDetails->filter(function ($record) {
                return $record->status == 1;
            });

            $inActiveRecords = $pageDetails->filter(function ($record) {
                return $record->status == 0;
            });

            // $perPage = 2; // Number of records per page for active records

            // // $activeRecords = $activeRecords->paginate($perPage);
            // // $inActiveRecords = $inActiveRecords->paginate($perPage);

            // $activeRecords = $activeRecords->forPage(Paginator::resolveCurrentPage('activePage'), $perPage);
            // $inActiveRecords = $inActiveRecords->forPage(Paginator::resolveCurrentPage('inactivePage'), $perPage);

            // $activeRecords = new LengthAwarePaginator(
            //     $activeRecords,
            //     $activeRecords->count(),
            //     $perPage,
            //     Paginator::resolveCurrentPage('activePage'),
            //     ['path' => Paginator::resolveCurrentPath()]
            // );

            // $inActiveRecords = new LengthAwarePaginator(
            //     $inActiveRecords,
            //     $inActiveRecords->count(),
            //     $perPage,
            //     Paginator::resolveCurrentPage('inactivePage'),
            //     ['path' => Paginator::resolveCurrentPath()]
            // );
            $pageDetails = [$activeRecords, $inActiveRecords];
            
            return $pageDetails;
        
        } catch (\Throwable $th) {
            throw $th;
        }
        
        return $pageDetails;
    }

    public function getCategoryDetails()
    {
        try {
            $getCategoryDetails = Categories::get()->toArray();
            return $getCategoryDetails;
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}