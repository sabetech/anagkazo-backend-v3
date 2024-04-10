<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentAPIController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\PastoralPointController;

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

Route::get('/students/ajax/search-student-only', [StudentController::class, 'searchStudentsOnly']);
Route::get('/pastoral_point_input', [StudentController::class, 'pastoral_point_input'])->name('get_pastoral_student');
Route::post('/post_pastoral_point', [StudentController::class, 'pastoral_point_input_post']);

Route::post('/v2/pastoral_points_form', [PastoralPointController::class, 'pastoral_point_form'])->name("pastoral_point_form");
Route::post('/v2/pastoral_points_submit', [PastoralPointController::class, 'pastoral_form_submit'])->name('pastoral_form_submit');
