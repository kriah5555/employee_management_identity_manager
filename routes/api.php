<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Permissions\PermissionsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/testing', function () {
    return response()->json([
        'message' => 'Test API.'
    ]);
});

Route::get('/permissions/setup', [PermissionsController::class, 'testing']);

Route::get('/manage-user/{user_id?}', [UserController::class, 'manageUsers']);

Route::post('/create-user', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('validate.api.token')->group(function () {

    Route::get('/user-details', [UserController::class, 'getUserDetails']);

    Route::get('/logout', [AuthController::class, 'logout']);

    Route::get('/validate-token', function () {
        return response()->json([
            'success' => true,
            'uid'     => Auth::guard('api')->user()->id
        ]);
    });
});