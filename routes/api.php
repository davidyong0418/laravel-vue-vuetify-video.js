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
Route::get('admin/video-management', 'Admin\VideoController@get_videos');
Route::post('admin/video-management/create', 'Admin\VideoController@create');
Route::post('admin/video-management/select_video', 'Admin\VideoController@select_video');

// Question management
Route::get('admin/question-management/read', 'Admin\QuestionController@get_questions');
Route::post('admin/question-management/create', 'Admin\QuestionController@create');
Route::post('admin/question-management/update', 'Admin\QuestionController@update');
Route::post('admin/question-management/delete', 'Admin\QuestionController@delete');
//Step management
Route::get('admin/step-management', 'Admin\StepController@get_questions');
Route::post('admin/step-management/create', 'Admin\StepController@create');
Route::post('admin/step-management/update', 'Admin\StepController@update');
Route::post('admin/step-management/delete', 'Admin\StepController@delete');
Route::post('admin/step-management/get_steps', 'Admin\StepController@get_steps');
Route::get('admin/step-management/get_init_data', 'Admin\StepController@get_init_data');
//User management
Route::post('user/user-quiz', 'User\QuizController@get_quiz_info');
Route::post('user/user-quiz/get_questions_answers', 'User\QuizController@get_questions_answers');
Route::post('user/user-quiz/accept', 'User\QuizController@accept');
Route::post('user/user-quiz/get_review_result', 'User\QuizController@get_review_result');
