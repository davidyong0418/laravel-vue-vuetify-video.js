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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix'=>'v1','middleware' => 'auth:api'], function() {
    Route::put('/user', 'LoggedUserController@update');
});
Route::get('admin/video-management', 'VideoController@get_videos');
Route::post('admin/video-management/create', 'VideoController@create');
Route::post('admin/video-management/select_video', 'VideoController@select_video');

// Question management
Route::get('admin/question-management', 'QuestionController@get_questions');
Route::post('admin/question-management/create', 'QuestionController@create');
Route::post('admin/question-management/update', 'QuestionController@update');
Route::post('admin/question-management/delete', 'QuestionController@delete');
//Step management
Route::get('admin/step-management', 'StepController@get_questions');
Route::post('admin/step-management/create', 'StepController@create');
Route::post('admin/step-management/update', 'StepController@update');
Route::post('admin/step-management/delete', 'StepController@delete');
Route::post('admin/step-management/get_steps', 'StepController@get_steps');
Route::get('admin/step-management/get_init_data', 'StepController@get_init_data');

//User management
// Route::get('user/user-quiz/get_quiz_info', 'UserController@get_quiz_info');
Route::get('user/user-quiz', 'UserController@index');
Route::post('user/user-quiz/get_questions_answers', 'UserController@get_questions_answers');
Route::post('user/user-quiz/accept', 'UserController@accept');
