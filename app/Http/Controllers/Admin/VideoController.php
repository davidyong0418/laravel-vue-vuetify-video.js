<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Http\Controllers\Controller;
class VideoController extends Controller
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
        
        return view('admin/video');
    }
    public function get_videos()
    {
        $videos = Video::all();
        $selected_video_id = Video::where('select', 1)->get()->toArray();
        $response = array(
            'videos' => $videos,
            'selected_video' =>$selected_video_id[0]['_id']
        );
       return response()->json($response);
    }
    public function create(Request $request)
    {
        $result = $request->get('data');
        $post = (array)json_decode($result);
        $vimeo_url = $post['video_url'];
        $vimeo_alias = $post['video_alias'];
        $vimeo_description = $post['video_description'];
        $thumbnail_link = $this->grab_vimeo_thumbnail($vimeo_url);
        if($thumbnail_link == false)
        {
            $response = array(
                'action' => false,
                'result' => "Vimeo URL isn't existed"
            );
        }
        else{
            $video = new Video();
            $video->alias = $vimeo_alias;
            $video->vimeo_url = $vimeo_url;
            $video->description = $vimeo_description;
            $video->thumbnail = $thumbnail_link;
            $video->select = 0;
            $video->save();
            $videos = Video::all();
            $response = array(
                'action' => true,
                'result' => $videos
            );
        }
        return response()->json($response);
    }
    public function grab_vimeo_thumbnail($vimeo_url){
        if( !$vimeo_url ) return false;
        $data = json_decode( file_get_contents( 'http://vimeo.com/api/oembed.json?url=' . $vimeo_url ) );
        if( !$data ) return false;
        return $data->thumbnail_url;
    }
    public function select_video(Request $request)
    {
        $selected_id = $request->get('data');
        Video::where('_id','!=',$selected_id)->update(['select'=>0]);
        Video::where('_id',$selected_id)->update(['select'=>1]);
        return ['action'=>'success'];
    }
    
}
