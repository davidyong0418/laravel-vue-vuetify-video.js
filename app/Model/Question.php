<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
 
class Question extends Model
{
    //
    protected $fillable = [
        'question','correct_answer'
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
