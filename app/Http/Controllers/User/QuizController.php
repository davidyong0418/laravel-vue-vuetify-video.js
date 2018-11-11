<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Step;
use App\Model\Question;
use App\Model\Userhistory;
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
        $user_id = $request->get('data');
        $user_quiz_data = Userhistory::where('user_id', $user_id)->get()->toArray();
        $response = array(
            'review_data' => $user_quiz_data[0]['step']
        );
        return response()->json($response);
    }
    public function get_quiz_info(Request $request)
    {

        $user_id = $request->get('data');
        $is_history = History::where('user_id', $user_id)->get()->toArray();
        $selectedSteps = Video::select('*')
            ->join("steps",function ($join){
                $join->on("steps.video_id", "=", "videos.id");
            })->where('videos.select', 1)->get()->toArray();
        print_r($selectedSteps);
        exit();
//        $historySteps = History::select('*')
//                ->join("steps",function($join){
//                    $join->on("steps.id","!=","histories.step_id")
//                        ->on('steps.video_id',"=",'histories.video_id');
//                })
//                ->where('histories.user_id', $user_id)
//                ->get()->toArray();
//
        $response = array(
            'historySteps' => $historySteps,
            'is_history' => $is_history
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
        $questionAnswers = Answer::select('*')->leftjoin("questions",function($join){
            $join->on("questions.id","=","answers.questionId");
        })->whereIn('questionId', $question_array)->get()->toArray();
        return ['questionAnswers' => $questionAnswers];
    }
    public function accept(Request $request)
    {
        $response_data = (array)json_decode($request->get('data'));
        $selected_ids = $response_data['selected_ids'];
        $current_quiz = $response_data['current_quiz'];
        $user_id = $response_data['user_id'];
        $flag = false;
        foreach ($current_quiz as $key => $item)
        {
            $current_quiz[$key]->user_select= $selected_ids[$key];
            unset($current_quiz[$key]->updated_at);
            unset($current_quiz[$key]->created_at);
            if($current_quiz[$key]->selected == $selected_ids[$key] + 1 )
            {
                $flag = true;
            }
            else{
                $flag = false;
            }
        }
        $check = Userhistory::where('user_id', $user_id)->get()->toArray();

        if($flag == true)
        {
            if(empty($check))
            {
                $new = array();
                $new[]= $current_quiz;
                $userhistory = new Userhistory();
                $userhistory->user_id = $user_id;
                $userhistory->step = $new;
                $userhistory->save();
            }
            else{
                $new = array();
                $new[]= $current_quiz;
                Userhistory::where('user_id', $user_id)->push('step', $new);
            }
        }
        return ['check'=>$flag];
    }
    public function view()
    {
        $video_data = Video::where('select', 1)->get()->toArray();
        return view('user/user', ['vimeo_url' => $video_data[0]['vimeo_url']]);
    }

    
}
