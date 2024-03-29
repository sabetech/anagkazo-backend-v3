<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAPIController;
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

Route::get('/health', function () {
    return response()->json(['status' => 'ok', 'method' => 'GET']);
});

Route::post('/health', function () {
    return response()->json(['status' => 'ok', 'method' => 'POST']);
});

Route::group(['prefix'=>'pwa'], function () {
    Route::post('verify-indexnumber', [StudentAPIController::class, 'verifyIndexNumber']);
    Route::post('authenticate', [StudentAPIController::class, 'authenticate']);
    Route::get('pastoral-points/{indexNumber}', [StudentAPIController::class, 'getPastoralPoints']);
    Route::get('bussing/{indexNumber}', [StudentAPIController::class, 'getBussing']);
    Route::post('attendance/{id}', [StudentAPIController::class, 'postAttendance']);
    Route::post('attendance/{indexNumber}/sync', [StudentAPIController::class, 'syncAttendance']);
    Route::get('attendance/{indexNumber}', [StudentAPIController::class, 'getAttendance']);
    Route::post('bussing/{indexNumber}', [StudentAPIController::class, 'postBussing']);
});

Route::group(['prefix'=>'admin'], function() {
    Route::get('/classes', [StudentAPIController::class, 'classes']);
});
