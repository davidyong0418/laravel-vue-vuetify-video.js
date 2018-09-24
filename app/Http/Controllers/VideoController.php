<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Video;
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
    
}
