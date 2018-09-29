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
        $requests = $request->get('data');
        $new = (array)json_decode($requests);
        $selected = $new['selected'] - 1 ;
        $new['answers'][$selected]->valid = 1;
        unset($new['_id']);
        $new['correct_answer'] = $new['answers'][$selected]->answer;
        Question::create($new);
        $questions = Question::all();
        return ['questions' => $questions];
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
        return['send' => $send];
        // return response()->json($update_data);
    }
    public function delete(Request $request)
    {
        $delete_id=$request->get('data');
        $question = Question::find($delete_id);
        $question->delete();
        // Question::where('_id', $delete_id)->delete();
        return 'success';
    }
    
}
