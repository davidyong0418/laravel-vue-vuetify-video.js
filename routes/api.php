<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix'=>'v1','middleware' => 'auth:api'], function() {
    Route::put('/user', 'LoggedUserController@update');
});
Route::get('admin/video-management', 'VideoController@get_videos');
Route::post('admin/video-management/create', 'VideoController@create');
// Question management
Route::get('admin/question-management', 'QuestionController@get_questions');
Route::post('admin/question-management/create', 'QuestionController@create');
Route::post('admin/question-management/update', 'QuestionController@update');
Route::post('admin/question-management/delete', 'QuestionController@delete');
