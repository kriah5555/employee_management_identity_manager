<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Roles\RolesController;

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

Route::post('/roles/create', [RolesController::class,'storeRole']);

Route::get('/get-roles', [RolesController::class,'manageRole']);

Route::get('/permissions/manage/{editid?}', [PermissionsController::class,'getPermissions']);

Route::post('/create-user', [UserController::class, 'createUser']);

Route::get('/manage-user/{user_id?}', [UserController::class, 'manageUsers']);

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth:sanctum');