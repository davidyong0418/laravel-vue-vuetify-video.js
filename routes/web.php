<?php

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// admin
Route::post('/password/email', 'PasswordController@remember');
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', 'AdminController@index')->name('home');
    Route::get('/admin/video-management', 'AdminController@video');
    Route::get('/admin/question-management', 'AdminController@question');
    Route::get('/admin/step-management', 'AdminController@step');
});

// user
Route::get('/user', 'UserController@index')->name('user');
Route::get('/user/user-course','UserController@view');
