<?php
namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model;
 
class Question extends Model
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'question';
    
    protected $fillable = [
        'count','question','answers','selected','correct_answer'
    ];
}
