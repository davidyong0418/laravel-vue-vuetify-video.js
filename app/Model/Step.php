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
    public function get_by_video($video_id = NULL)
    {
        $step = Step::raw(function($collection) use($video_id) {
            return $collection->aggregate([
                [
                    '$match' =>
                    [
                        'video_id' =>$video_id
                    ]
                ],
                [
                    '$project'=> 
                    [
                        'object_video_id'=> ['$toObjectId'=> '$video_id'],
                        'video_id'=>1,
                        'end_times'=>1,
                        'question_ids'=>1,
                    ]
                ],
                [
                 '$lookup'=>
                    [
                        'from'=>'video',
                        'localField'=>'object_video_id',
                        'foreignField'=> '_id',
                        'as'=>'videos'
                    ]
                ],
                [
                    '$addFields'=> 
                    [
                        'alias' => '$videos.alias',
                        'vimeo_url' => '$videos.vimeo_url'
                    ]
                ]
            ]);
        })->toArray();
        return $step;
    }
}
