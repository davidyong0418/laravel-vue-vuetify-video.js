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
Route::post('/password/email', 'PasswordController@remember');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/admin/video-management', 'VideoController@index');
Route::get('/admin/question-management', 'QuestionController@index');
Route::get('/admin/step-management', 'StepController@index');
Route::get('/user/user-course','UserController@view');
