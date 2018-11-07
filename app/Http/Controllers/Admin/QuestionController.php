<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Question;
use App\Model\Answer;
use DB;
use App\Http\Controllers\Controller;
class QuestionController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin/question');
    }
    // Get all questions
    public function show()
    {
        $questions = Question::all();
        return ['questions' => $questions];
    }
    // Create question
    public function update(Request $request)
    {
        $requests = $request->get('data');
        $question = $request->get('question');
        $selection = $request->get('selection');
        $new = (array)json_decode($requests);
        $action = $request->get('action');
        $correct_answer = '';
        $new_question  = [];
        if($action == 'update')
        {
            $old_question = $new[0]->questionId;
            Answer::where('questionId', $old_question)->delete();
            // Question::where('id', $old_question)->update(array('question' => $question));
            Question::where('id', $old_question)->delete();
        }
        $createdQuestion = Question::insert(array('question' => $question));
        $questionId = DB::getPdo()->lastInsertId();
        foreach ($new as $item)
        {
            $sub_question = [];
            $sub_question['questionId'] = $questionId;
            if($item->id == $selection)
            {
                $sub_question['correct_answer'] = $item->answer;
                $correct_answer = $item->answer;
            }
            else{
                $sub_question['correct_answer'] = '';
            }
            $sub_question['answer'] = $item->answer;

            array_push($new_question, $sub_question);
        }
        Answer::insert($new_question);
        Question::where('id', $questionId)->update(array('correct_answer' => $correct_answer));
        $questions = Question::all();
        return ['questions' => $questions];
    }
    // Update question
    public function delete(Request $request)
    {
        $id = $request->get('deleteQuestion');
        Question::where('id', $id)->delete();
        Answer::where('questionId', $id)->delete();
        $questions = Question::all();
        return ['questions' => $questions];
    }
    public function edit(Request $request)
    {
        $questionId = $request->get('questionId');
        $question = Question::where('id', $questionId)->get()->toArray();
        $answers = Answer::where('questionId', $questionId)->get()->toArray();
        $response = array(
            'question' => $question[0]['question'],
            'answers' => $answers
        );
        return response()->json($response);
    }
    
}
