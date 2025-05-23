<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAPIController;
use App\Http\Controllers\FellowshipServiceAPIController;
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
    Route::get('/student/{index_number}/image', [StudentAPIController::class, 'getStudentImage']);
    Route::post('authenticate', [StudentAPIController::class, 'authenticate']);
    Route::get('pastoral-points/{indexNumber}', [StudentAPIController::class, 'getPastoralPoints']);
    Route::get('bussing/{indexNumber}', [StudentAPIController::class, 'getBussing']);
    Route::post('attendance/{id}', [StudentAPIController::class, 'postAttendance']);
    Route::post('attendance/{indexNumber}/sync', [StudentAPIController::class, 'syncAttendance']);
    Route::get('attendance/{indexNumber}', [StudentAPIController::class, 'getAttendance']);
    Route::post('bussing/{indexNumber}', [StudentAPIController::class, 'postBussing']);
    Route::post('fellowship_service/{indexNumber}', [StudentAPIController::class, 'postFellowshipService']);
    Route::get('fellowship_service/{indexNumber}', [StudentAPIController::class, 'getFellowshipService']);
    Route::post('fellowship_service/{indexNumber}/cancel', [StudentApiController::class, 'cancelFellowshipService']);
});

Route::group(['prefix'=>'admin'], function() {
    Route::get('/classes', [StudentAPIController::class, 'classes']);
    Route::get('/fellowship_services', [FellowshipServiceAPIController::class, 'fellowshipServices']);
});
