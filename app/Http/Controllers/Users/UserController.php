<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\Users\Services\UserServices;

class UserController extends Controller
{
    /**
     * creating objects
     */
    protected $serviceobj;
    
    /**
     * class constructor
     */

     public function __construct()
     {
        $this->serviceobj = new UserServices();
     }
     
    public function createUser(Request $request)
    {
        try {
            $saved = $this->serviceobj->createUserService($request);

            if ($saved) {
                return response()->json([
                    'status' => 200,
                    'message' => "User created successfully"
                ], 200);
            } else {
                return response()->json([
                    'status' => 500,
                    'message' => "Error in creating the user"
                ], 500);
            }
        } catch (\Exception $ex) {
            return response()->json([
                'status' => 400,
                'message' => $ex->getMessage()
            ], 400);
        }
    }

    public function manageUsers($userid = null)
    {
        $supervisor = null;
        if ($userid == 'supervisor') {
            $supervisor = $userid;
            $userid = null;
        }
        $data = $this->serviceobj->manageUserService($userid, $supervisor);

        if ($data) {
            return response()->json([
                'status' => 200,
                'message' => "User fetched successfully",
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => 500,
                'message' => "Error in fetching the user"
            ], 500);
        }
    }
}
