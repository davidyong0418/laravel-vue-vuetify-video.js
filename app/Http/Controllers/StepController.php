<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Step;
use App\Moedel\Question;
class StepController extends Controller
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
    public function index()
    {
        return view('admin/step');
    }
    public function get_questions()
    {
        $steps = Step::all();
       return ['steps' => $steps];
    }
    public function get_videos(Request $request)
    {
        $videos = Video::all();
        $questions = Question::all();
        $steps = Step::all();
        return ['videos'=> $videos, 'questions'=>$questions, 'init_steps'=>$init_steps];
    }
    public function get_init_data(Request $request)
    {
        $video_id = $request->get('data');
        $steps = Step::where('video_id', $video_id)->get();
        return ['steps'=>$steps];
    }
    public function create(Request $request)
    {
        // $requests = $request->get('data');
        // $new = (array)json_decode($requests);

        // Step::create($new);
            
        $step = new Step();
        $step->video_id = 10;
        $step->end_times = array(
            'step'=>'00:15',
            'sort'=> 1
        );
        $step->question_ids = '1,2,3,4,5';
        $step->save();
        // $step = Step::all();
        return ['step' => $step];

    }
    public function update(Request $request)
    {
        $result = $request->get('data');
        $update = (array)json_decode($result);
        $update_id = $update['_id'];
        $selected = $update['selected'];
        if($selected > $update['count'])
        {
            $selected = 1;
        }
        $correct_answer = $update['answers'][$selected-1]->answer;
        $update_data = array(
            'question' => $update['question'],
            'count' => $update['count'],
            'answers' => $update['answers'],
            'selected' => $update['selected'],
            'correct_answer' => $correct_answer,
        );
        Question::where('_id', $update_id)->update($update_data);
        $send = array(
            'action' => 'true',
            'result'=>'success'
        );
        return $update_data;
    }
    public function delete(Request $request)
    {
        $delete_id=$request->get('data');
        $question = Question::find($delete_id);
        Question::where('_id', $delete_id)->delete();
        return 'success';
    }
    
}
