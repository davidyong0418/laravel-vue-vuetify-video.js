<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\PasswordController;
use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Step;
use App\Model\Question;
use App\Model\History;
use App\Model\Answer;
use JavaScript;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Http\Controllers\Controller;
use DB;
class QuizController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function get_review_result(Request $request)
    {
        $request = $request->get('data');
        $user_id = $request['user_id'];
        $vimeo_id = $request['vimeo_id'];

        $historyData = History::where('user_id', 1)->get()->toArray();
        History::where('user_id', $user_id)->update(array('pass_status' => 1));
        $all_question_answers = [];
        foreach($historyData as $key => $historyItem)
        {
            $question_array = explode(',', $historyItem['question_ids']);
            
            foreach ($question_array as $item)
            {
                $result = Answer::select('*')->leftjoin("questions",function($join){
                    $join->on("questions.id", "=", "answers.questionId");
                })->where('answers.questionId', $item)->where('answers.correct_answer', '!=', '')->get()->toArray();
                $result[0]['step'] = $key + 1;
                array_push($all_question_answers, $result[0]);
            }
        }
        return ['result' => $all_question_answers];
    }
    public function show(Request $request)
    {
        $user_id = $request->get('user_id');
        $vimeoid = $request->get('vimeoid');
        $historySteps = History::where('user_id', $user_id)->where('video_id', $vimeoid)->get()->toArray();
        $passSteps = History::where('user_id', $user_id)->where('video_id', $vimeoid)->where('pass_status', 1)->get()->toArray();
        if(empty($historySteps))
        {
            $selectedSteps = Video::select('*')
                ->join("steps",function ($join){
                    $join->on("steps.video_id", "=", "videos.id");
                })->where('videos.select', 1)->get()->toArray();
            foreach ($selectedSteps as $step){
                $history = new History();
                $history->user_id = $user_id;
                $history->video_id = $step['video_id'];
                $history->point = $step['point'];
                $history->old_point = $step['old_point'];
                $history->question_ids = $step['question_ids'];
                $history->pass_status = 0;
                $history->save();
            }
            $historySteps = History::where('user_id', $user_id)->get()->toArray();
        }

        $response = array(
            'isPass' => empty($passSteps)?false:true,
            'historyStep' => $historySteps
        );
        return response()->json($response);
    }
    public function get_videos()
    {
        $videos = Video::all();
        return ['videos' => $videos];
    }
    public function create(Request $request)
    {
        $vimeo_url = $request->get('video_url', TRUE);
        $vimeo_alias = $request->get('video_alias', TRUE);
        $video = new Video();
        $video->alias = $vimeo_alias;
        $video->vimeo_url = $vimeo_url;
        $video->save();
        $videos = Video::all();
        return ['videos' => $videos];

    }
    public function get_questions_answers(Request $request)
    {
        $question_ids = $request->get('data');
        $question_array = explode(',', $question_ids);
        $all_question_answers = [];
        foreach ($question_array as $item)
        {
            $result = Answer::select('*')->leftjoin("questions",function($join){
                $join->on("questions.id","=","answers.questionId");
            })->where('answers.questionId', $item)->get()->toArray();
            array_push($all_question_answers, $result);
        }
        return response()->json($all_question_answers);
    }
    public function accept(Request $request)
    {
        $response_data = (array)json_decode($request->get('data'));
        $correct = true;
        foreach ($response_data as $item)
        {
            $check = Answer::where('correct_answer', $item->answer)->where('questionId', $item->questionId)->get()->toArray();
            if(empty($check))
            {
                $correct = false;
                break;
            }
        }
        return ['check' => $correct];
    }
    public function view()
    {
        $video_data = Video::where('select', 1)->get()->toArray();
        return view('user/user', ['vimeo_url' => $video_data[0]['vimeo_url']]);
    }
    
}
