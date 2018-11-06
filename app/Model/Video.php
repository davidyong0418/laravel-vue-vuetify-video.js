<?php
namespace App\Model;

use Illuminate\Database\Eloquent\Model;
class Video extends Model
{
    //

    protected $fillable = [
        'alias','vimeo_url','thumbnail','select','description'
    ];
}
