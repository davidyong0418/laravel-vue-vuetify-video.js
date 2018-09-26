<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Question;
class QuestionController extends Controller
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
        return view('admin/question');
    }
    public function get_questions()
    {
        $questions = Question::all();
       return ['questions' => $questions];
    }
    public function create(Request $request)
    {
        $vimeo_url = $request->all();
        var_dump($vimeo_url);
        exit;
        $vimeo_alias = $request->get();
        $video = new Question();
        $video->alias = $vimeo_alias;
        $video->vimeo_url = $vimeo_url;
        $video->save();
        $videos = Question::all();
        return ['videos' => $videos];

    }
    
}
