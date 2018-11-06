<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
 
class Question extends Model
{
    //

    protected $fillable = [
        'count','question','answers','selected','correct_answer'
    ];
    public function all_questions()
    {
        $questions = Question::groupBy('question')->get()->toArray();
        return $questions;
    }
}
