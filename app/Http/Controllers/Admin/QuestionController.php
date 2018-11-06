<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Model\Video;
use App\Model\Question;
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
        $question_obj = new Question();
        $questions = $question_obj->all_questions();
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
        if($action == 'update')
        {
            $old_question = $new[0]->question;
            Question::where('question', $old_question)->delete();
        }
        $new_question  = [];
        foreach ($new as $item)
        {
            $sub_question = [];
            $sub_question['question'] = $question;
            if($item->id == $selection)
            {
                $sub_question['correct_answer'] = $item->answer;
            }
            else{
                $sub_question['correct_answer'] = '';
            }
            $sub_question['answer'] = $item->answer;
            array_push($new_question, $sub_question);
        }
        Question::insert($new_question);
        $question_obj = new Question();
        $questions = $question_obj->all_questions();
       return ['questions' => $questions];
    }
    // Update question
    public function delete(Request $request)
    {
        $deleteQuestion = $request->get('deleteQuestion');
        Question::where('question', $deleteQuestion)->delete();
        $question_obj = new Question();
        $questions = $question_obj->all_questions();
        return ['questions' => $questions];
    }
    public function edit(Request $request)
    {
        $itemQuestion = $request->get('itemQuestion');
        $editQuestions = Question::where('question', $itemQuestion)->get()->toArray();
        return response()->json($editQuestions);
    }
    
}
