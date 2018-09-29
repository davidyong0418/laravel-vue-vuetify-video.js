<?php
namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model;
 
class Step extends Model
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'step';
    
    protected $fillable = [
        'video_id','end_times'
    ];
}
