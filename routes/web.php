<?php

use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\KmlReaderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'KmlReaderController@index');
Route::post('/upload', 'KmlReaderController@upload')->name('upload');
Route::post('/calculate', 'KmlReaderController@calculate')->name('calculate');
Route::get('/calculate', [KmlReaderController::class, 'coordinate_inside']);
