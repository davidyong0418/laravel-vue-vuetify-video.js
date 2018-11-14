<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Video;
use Session;
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user_id = Auth::user()->id;
        $video_data = Video::where('select', 1)->get()->toArray();
        $vimeo_url = '';
        $vimeo_id = '';
        if(!empty($video_data))
        {
            $vimeo_url = $video_data[0]['vimeo_url'];
            $vimeo_id = $video_data[0]['id'];
        }
        return view('user/user', ['vimeo_url' => $vimeo_url, 'distinct' => $user_id, 'vimeo_id' => $vimeo_id]);
    }
    
}
