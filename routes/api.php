<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');



Route::post('/login', 'App\Http\Controllers\API\RegisterController@login')->middleware(['cors']);
Route::post('users/getfromtoken','App\Http\Controllers\API\UserController@getUserfromtoken')->middleware('cors');
Route::post('test','App\Http\Controllers\API\DeviceController@test')->middleware('cors');

Route::middleware(['auth','cors'])->group( function () {
    setPermissionsTeamId('1');
    Route::get('/guard',function(){
        dd(auth());
    });
    Route::get('/users/{id?}', 'App\Http\Controllers\API\UserController@Users')->middleware(['check_team','permission:Manage Users']);;
    Route::post('/users', 'App\Http\Controllers\API\UserController@storeUser')->middleware(['permission:Create User']);
    Route::put('/users', 'App\Http\Controllers\API\UserController@UpdateUser')->middleware(['permission:Manage Users']);
    Route::post('/users/delete', 'App\Http\Controllers\API\UserController@DeleteUser')->middleware(['permission:Manage Users']);

    Route::get('/profile', 'App\Http\Controllers\API\UserController@profile');
    Route::put('/profile', 'App\Http\Controllers\API\UserController@profileUpdate');


    Route::get('/teams/{id?}','App\Http\Controllers\API\TeamController@Show')->middleware(['permission:Manage Teams']);
    Route::post('/teams','App\Http\Controllers\API\TeamController@Create')->middleware(['permission:Create Teams']);
    Route::put('/teams','App\Http\Controllers\API\TeamController@UpdateTeam')->middleware(['permission:Edit Teams']);
    Route::post('/teams/delete', 'App\Http\Controllers\API\TeamController@DeleteTeam')->middleware(['permission:Delete Teams']);
    
    Route::get('/roles/{id?}','App\Http\Controllers\API\RoleController@Show')->middleware(['permission:Manage Roles']);
    Route::post('/roles','App\Http\Controllers\API\RoleController@Create')->middleware(['permission:Manage Roles']);
    Route::put('/roles','App\Http\Controllers\API\RoleController@UpdateRole')->middleware(['permission:Manage Roles']);
    Route::post('/roles/delete', 'App\Http\Controllers\API\RoleController@DeleteRole')->middleware(['permission:Manage Roles']);

    Route::get('/permissions/{id?}','App\Http\Controllers\API\PermissionController@Show')->middleware(['permission:Manage Permissions']);
    Route::post('/permissions','App\Http\Controllers\API\PermissionController@Create')->middleware(['permission:Manage Permissions']);
    Route::put('/permissions','App\Http\Controllers\API\PermissionController@UpdatePermission')->middleware(['permission:Manage Permissions']);
    Route::post('/permissions/delete', 'App\Http\Controllers\API\PermissionController@DeletePermission')->middleware(['permission:Manage Permissions']);

});

