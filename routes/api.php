<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Roles\RolesController;


use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\{GenderController, MaritalStatusController,  LanguagesController, UserController};
use App\Http\Controllers\ChatController;
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

Route::post('/web-login', [AuthController::class, 'webLogin']);

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

Route::controller(LanguagesController::class)->group(function () {
    Route::post('language/all', 'index');
    Route::post('language/store', 'store');
    Route::post('language/{language}', 'show');
    Route::post('language/edit/{language}', 'edit');
    Route::post('language/delete/{language}', 'destroy');
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
});

Route::get('/employee/options', [UserController::class, 'getEmployeeCreationOptions']);
Route::post('employee/create', [UserController::class, 'createEmployee']);
Route::post('employee/invite', [UserController::class, 'inviteEmployee']);

// Route::get('/employee/options', [UserController::class, 'getEmployeeCreationOptions']);
// Route::post('employee/create', [UserController::class, 'createEmployee']);
// Route::post('employee/invite', [UserController::class, 'inviteEmployee']);


// Check if a conversation exists between two users or create a new one

Route::post('/check-or-create-conversation', [ChatController::class, 'createConversation']);
Route::post('/send-message', [ChatController::class, 'sendMessage']);
Route::post('/get-conversation', [ChatController::class, 'getMessagesInConversationFormat']);
Route::delete('/delete-conversation', [ChatController::class, 'deleteConversation']);
Route::delete('/delete-message', [ChatController::class, 'deleteMessage']);


//forgot password

Route::post('employee/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('employee/reset-password', [UserController::class, 'resetPassword']);


Route::get('user/get-options-for-user-basic-details', [UserController::class, 'getOptionsForUserBasicDetails']);

$resources = [
    'genders'          => [
        'controller' => GenderController::class,
        'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
    ],
    'marital-statuses' => [
        'controller' => MaritalStatusController::class,
        'methods'    => ['index', 'show', 'create', 'store', 'update', 'destroy']
    ],
];
foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
    Route::resource($uri, $controller)->only($methods);
}
Route::get('employee/get-dependent-spouse-options', [UserController::class, 'getDependentSpouseOptions']);
Route::get('employee/get-language-options', [UserController::class, 'getLanguageOptions']);


Route::group(['middleware' => 'setactiveuser'], function () {
    $resources = [
        'user' => [
            'controller' => UserController::class,
            'methods'    => ['create']
        ],
    ];
    foreach ($resources as $uri => ['controller' => $controller, 'methods' => $methods]) {
        Route::resource($uri, $controller)->only($methods);
    }
});

//update user details
Route::put('update-employee', [UserController::class, 'updateEmployee']);
// Check if a conversation exists between two users or create a new one

Route::post('/check-or-create-conversation', [ChatController::class, 'createConversation']);
Route::post('/send-message', [ChatController::class, 'sendMessage']);
Route::get('/get-conversation/{conversationId}', [ChatController::class, 'getMessagesInConversationFormat']);
Route::get('/get-messages/{conversationId}', [ChatController::class, 'getMessages']);
Route::delete('/conversation/{id}', [ChatController::class, 'deleteConversation']);
Route::delete('/message/{id}', [ChatController::class, 'deleteMessage']);


//forgot password

Route::post('employee/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('employee/reset-password', [UserController::class, 'resetPassword']);





