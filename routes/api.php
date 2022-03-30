<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::controller(UserController::class)->group(function () {

    Route::post('user/insert','create');
    Route::post('user/update','update')->middleware('jwt.verify');
    Route::get('user/list','list')->middleware('jwt.verify');
    Route::get('user/list/all','listAll')->middleware('jwt.verify');
    Route::get('user/list/delete','listSoftDelete')->middleware('jwt.verify');
    Route::post('user/delete','delete')->middleware('jwt.verify');
    Route::post('user/soft/delete','softDelete')->middleware('jwt.verify');
    Route::post('user/restore','restore')->middleware('jwt.verify');
    Route::post('user/change/password','changePassword')->middleware('jwt.verify');
    Route::post('user/login','login');
});

// Route::get('user/list', [UserController::class, 'list']);