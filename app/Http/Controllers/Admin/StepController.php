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
        $steps = Step::where('video_id', $video_id)->get()->toArray();
        return response()->json($steps);
    }
    public function show(Request $request)
    {
        $videos = Video::all();
        $questions = Question::all();
        $response = array(
            'videos'=>$videos,
            'questions'=>$questions,
        );
        return response()->json($response);
    }
    public function create(Request $request)
    {
        $requests = $request->get('data');
        $data = (array)json_decode($requests);
        $newSteps = [];
        $video_id = '';
        foreach ($data as $key =>$item)
        {
            $middleStep = [];
            if($key == 0)
            {
                $video_id = $item->video_id;
            }
            $middleStep['video_id'] = $video_id;
            $middleStep['point'] = $item->point;
            $middleStep['question_ids'] = implode(',', $item->questions);
            array_push($newSteps, $middleStep);
        }
        Step::where('video_id', $video_id)->delete();
        if (Step::insert($newSteps))
        {
            return 'success';
        }
    }
}
