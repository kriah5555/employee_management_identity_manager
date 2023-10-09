<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Roles\RolesController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\{GenderController, LanguagesController, MaritalStatusController};

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

Route::post('/roles/create', [RolesController::class, 'storeRole']);

Route::get('/get-roles', [RolesController::class, 'manageRole']);

Route::get('/permissions/manage/{editid?}', [PermissionsController::class, 'getPermissions']);

Route::get('/manage-user/{user_id?}', [UserController::class, 'manageUsers']);

Route::post('/create-user', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);

Route::post('/generate-access-token', [AuthController::class, 'generateAccessToken']);

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

Route::controller(GenderController::class)->group(function () {
    Route::post('gender/all', 'index');
    Route::post('gender/store', 'store');
    Route::post('gender/{gender}', 'show');
    Route::post('gender/edit/{gender}', 'edit');
    Route::post('gender/delete/{gender}', 'destroy');
});

Route::controller(LanguagesController::class)->group(function () {
    Route::post('language/all', 'index');
    Route::post('language/store', 'store');
    Route::post('language/{language}', 'show');
    Route::post('language/edit/{language}', 'edit');
    Route::post('language/delete/{language}', 'destroy');
});

// Route::controller(MaritalStatusController::class)->group(function() {
//     Route::post('marital/all', 'index');
//     Route::post('marital/store', 'store');
//     Route::post('marital/{marital_status}', 'show');
//     Route::post('marital/edit/{marital_status}', 'edit');
//     Route::post('marital/delete/{marital_status}', 'destroy');
// });

Route::group([
    // 'middleware' => ['admin','auth'],
    //if you have one more folder inside Controllers you can specify namespaces too
    'controller' => MaritalStatusController::class,
    'prefix'     => 'marital',
], function () {
    Route::post('all', 'index');
    Route::post('store', 'store');
    Route::post('/{marital_status}', 'show');
    Route::post('edit/{marital_status}', 'edit');
    Route::post('delete/{marital_status}', 'destroy');
});




Route::middleware('auth:api')->group(function () {
    Route::get('user', 'AuthController@user');
    Route::post('logout', 'AuthController@logout');
});

Route::group([
    // 'middleware' => ['admin','auth'],
    //if you have one more folder inside Controllers you can specify namespaces too
    'controller' => UserController::class,
    'prefix'     => 'user',
], function () {
    Route::post('create', 'createUser');
    Route::post('all', 'manageUsers');
    // Route::post('edit/{marital_status}', 'edit');
    // Route::post('delete/{marital_status}', 'destroy');
});

Route::get('/employee/options', [UserController::class, 'getEmployeeCreationOptions']);
Route::post('employee/create', [UserController::class, 'createEmployee']);
Route::post('employee/invite', [UserController::class, 'inviteEmployee']);

// Route::get('/employee/options', [UserController::class, 'getEmployeeCreationOptions']);
// Route::post('employee/create', [UserController::class, 'createEmployee']);
// Route::post('employee/invite', [UserController::class, 'inviteEmployee']);
