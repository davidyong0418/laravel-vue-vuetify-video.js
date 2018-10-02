<?php
namespace App\Model;

use Jenssegers\Mongodb\Eloquent\Model;
 
class Userhistory extends Model
{
    //
    protected $connection = 'mongodb';
    protected $collection = 'userhistory';
    
    protected $fillable = [
        'video_id','end_times'
    ];
}
