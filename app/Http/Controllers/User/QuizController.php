<?php

namespace App\Http\Controllers\User;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Step;
use App\Model\Question;
use App\Model\Userhistory;
use JavaScript;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Http\Controllers\Controller;
class QuizController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

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
        $user_info = Userhistory::where('user_id', $user_id)->get()->toArray();
        if(empty($user_info))
        {   
            $step_order = 0;
        }
        else{
            $step_order = count($user_info[0]['step']);
        }
        $video_data = Video::where('select', 1)->get()->toArray();
        $step_data = Step::where('video_id', $video_data[0]['_id'])->get()->toArray();
        $response = array(
            'video_data' => $video_data[0],
            'step_data' => $step_data[0],
            'step_order' => $step_order
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
        $question_ids = json_decode($request->get('data', TRUE));
        $questions = Question::whereIn('_id', $question_ids)->get();
        return ['questions' => $questions];
    }
    public function accept(Request $request)
    {
        $response_data = (array)json_decode($request->get('data'));
        $selected_ids = $response_data['selected_ids'];
        $current_quiz = $response_data['current_quiz'];
        // print_r($current_quiz);
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
