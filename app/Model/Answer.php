<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
 
class Answer extends Model
{
    //
    protected $fillable = [
        'questionId','correct_answer','answer'
    ];
}
