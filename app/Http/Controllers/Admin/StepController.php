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
    public function get_videos(Request $request)
    {
        $videos = Video::all();
        $questions = Question::all();
        $steps = Step::all();
        return ['videos'=> $videos, 'questions'=>$questions, 'init_steps'=>$init_steps];
    }
    public function get_steps(Request $request)
    {
        $video_id = $request->get('data');
        $step = Step::raw(function($collection) use($video_id) {
            return $collection->aggregate([
                [
                    '$match' =>
                    [
                        'video_id' =>$video_id
                    ]
                ],
                [
                    '$project'=> 
                    [
                        'object_video_id'=> ['$toObjectId'=> '$video_id'],
                        'video_id'=>1,
                        'end_times'=>1,
                        'question_ids'=>1,
                    ]
                ],
                [
                 '$lookup'=>
                    [
                        'from'=>'video',
                        'localField'=>'object_video_id',
                        'foreignField'=> '_id',
                        'as'=>'videos'
                    ]
                ],
                [
                    '$addFields'=> 
                    [
                        'alias' => '$videos.alias',
                        'vimeo_url' => '$videos.vimeo_url'
                    ]
                ]
            ]);
        })->toArray();
        
        if(!empty($step))
        {
            $response = array(
                'action'=> 'true',
                'steps'=> $step[0]
            );

        }else{
            $vimeo_url = Video::find($video_id);
            $response = array(
                'action'=> 'false',
                'steps' => $video_id,
            );
        }
        return response()->json($response);
    }
    public function get_init_data(Request $request)
    {
        $videos = Video::all();
        $questions = Question::raw(function($collection){
            return $collection->aggregate([
                [
                    '$project'=>[
                        'answers'=>0
                    ]
                ]
            ]);
        })->toArray();
        // $questions = Question::all
        $init_steps = Step::raw(function($collection){
            return $collection->aggregate([
                [
                    '$project'=> 
                    [
                        'object_video_id'=> ['$toObjectId'=> '$video_id'],
                        'video_id'=>1,
                        'end_times'=>1,
                        'question_ids'=>1,
                    ]
                ],
                [
                 '$lookup'=>
                    [
                        'from'=>'video',
                        'localField'=>'object_video_id',
                        'foreignField'=> '_id',
                        'as'=>'videos'
                    ]
                ],
                [
                    '$addFields'=> 
                    [
                        'alias' => '$videos.alias',
                        'vimeo_url' => '$videos.vimeo_url'
                    ]
                ]
            ]);
        })->toArray();
        if(empty($init_steps))
        {
           $action = 'false';
        }
        else{
           $action = 'false';
        }
        $send = array(
            'videos'=>$videos,
            'questions'=>$questions,
            'init_steps'=>$init_steps[0],
            'action' => $action
        );
        // return ['questions' => $questions];
        return response()->json($send);
    }
    public function create(Request $request)
    {
        $requests = $request->get('data');
        $new = (array)json_decode($requests);
        foreach ($new['end_times'] as $key =>$item)
        {
            if($key == 0)
            {
             $item->s_point = '00:00';
            }
            else{
                $item->s_point = $new['end_times'][$key - 1]->point;
            }
        }
        if(empty($new['_id']))
        {
            Step::create($new);
        }
        else{
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
