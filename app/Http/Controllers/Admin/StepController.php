<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Step;
use App\Model\Question;
use App\Http\Controllers\Controller;
class StepController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/step');
    }
    // Get all videos
    public function get_videos(Request $request)
    {
        $videos = Video::all();
        $questions = Question::all();
        $steps = Step::all();
        return ['videos'=> $videos, 'questions'=>$questions, 'init_steps'=>$init_steps];
    }
    // Get all steps
    public function get_steps(Request $request)
    {
        $video_id = $request->get('data');
        $step = new Step();
        $step_data = $step->get_by_video($video_id);
        if(!empty($step_data))
        {
            $response = array(
                'action'=> 'true',
                'steps'=> $step_data[0]
            );
            
        }else{
            $response = array(
                'action'=> 'false',
                'steps' => $video_id,
            );
        }
        return response()->json($response);
    }
    public function show(Request $request)
    {
        $videos = Video::all();
        $questionObj = new Question();
        $questions = $questionObj->all_questions();
        $response = array(
            'videos'=>$videos,
            'questions'=>$questions,
        );
        return response()->json($response);
    }
    public function create(Request $request)
    {
        $requests = $request->get('data');
        $new = (array)json_decode($requests);
        foreach ($new['end_times'] as $key =>$item)
        {
            if($key == 0)
            {
             $item->s_point = '0000';
            }
            else
            {
                $item->s_point = $new['end_times'][$key - 1]->point;
            }
        }

        if(empty($new['_id']))
        {
            Step::create($new);
        }
        else
        {
            $update_step = array(
                'video_id' => $new['video_id'],
                'end_times' => $new['end_times']
            );
            Step::where('_id',$new['_id'])->update($update_step);
        }
        $step = Step::all();
        return ['step' => $step];
    }
}
