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
        $id = Auth::user()->id;
        Session::put('user_id', $id);
        $video_data = Video::where('select', 1)->get()->toArray();
        if(!empty($video_data))
        {
            $response = $video_data[0]['vimeo_url'];
        }
        else
        {
            $response = '';
        }
        return view('user/user', ['vimeo_url' => $response, 'distinct' =>$id ]);
    }
    
}
