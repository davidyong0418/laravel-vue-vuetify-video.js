<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
 
class Step extends Model
{
    //
    protected $fillable = [
        'video_id', 'point', 'old_point', 'question_ids'
    ];
    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
