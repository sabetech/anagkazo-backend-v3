<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAPIController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/form/fellowship-bussing', 'StudentClientController@getFellowshipBussingForm')->name('getFellowshipBussingForm');
Route::get('/anagkazo/attendance/reports', [StudentAPIController::class, 'exportAttendance'])->name('exportAttendance');

Route::get('/pastoral_point_input', [StudentController::class, 'pastoral_point_input'])->name('get_pastoral_student');
Route::post('/post_pastoral_point', [StudentController::class, 'pastoral_point_input_post']);
