<?php
namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model;
 
class Video extends Model
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'video';
    
    protected $fillable = [
        'count','question','answers','selected','correct_answer'
    ];
}
