<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\Video;
use Session;
class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = $this->vimeoVideoDuration('https://vimeo.com/115134273');
        var_dump($data);
        exit;





        $id = Auth::user()->_id;
        Session::put('user_id', $id);
        $video_data = Video::where('select', 1)->get()->toArray();
        return view('user/user', ['vimeo_url' => $video_data[0]['vimeo_url'], 'distinct' =>$id ]);
    }
    public function vimeoVideoDuration($video_url) {

        $video_id = (int)substr(parse_url($video_url, PHP_URL_PATH), 1);
     
        $json_url = 'http://vimeo.com/api/v2/video/' . $video_id . '.xml';
     
        $ch = curl_init($json_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $data = curl_exec($ch);
        curl_close($ch);
        $data = new SimpleXmlElement($data, LIBXML_NOCDATA);
     
        if (!isset($data->video->duration)) {
            return null;
        }
     
        $duration = $data->video->duration;
     
        return $duration; // in seconds
     }
    
        function curl_get($url){
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_TIMEOUT, 30);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
            $return = curl_exec($curl);
            curl_close($curl);
            return $return;
          }
          
          function youtube_id_from_url($url){
          $pattern = '%^# Match any youtube URL
          (?:https?://)? # Optional scheme. Either http or https
          (?:www\.)? # Optional www subdomain
          (?: # Group host alternatives
          youtu\.be/ # Either youtu.be,
          | youtube\.com # or youtube.com
          (?: # Group path alternatives
          /embed/ # Either /embed/
          | /v/ # or /v/
          | /watch\?v= # or /watch\?v=
          ) # End path alternatives.
          ) # End host alternatives.
          ([\w-]{10,12}) # Allow 10-12 for 11 char youtube id.$%x';
          $result = preg_match($pattern, $url, $matches);
          if (false !== $result) {
          return $matches[1];
          }
          return false;
          }
          
          function getVideolength($videoid='') {
          define('YT_API_URL', 'http://gdata.youtube.com/feeds/api/videos?q=');
          $video_id = $videoid;
          //Using cURL php extension to make the request to youtube API
          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, YT_API_URL . $video_id);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          //$feed holds a rss feed xml returned by youtube API
          $feed = curl_exec($ch);
          curl_close($ch);
          
          //Using SimpleXML to parse youtube's feed
          $xml = simplexml_load_string($feed);
          $entry = $xml->entry[0];
          $media = $entry->children('media', true);
          $group = $media->group;
          $vid_duration = $content_attributes['duration'];
          $duration_formatted = str_pad(floor($vid_duration / 60), 2, 
          '0', STR_PAD_LEFT) . ':' . str_pad($vid_duration % 60, 2, '0', STR_PAD_LEFT);
          return $duration_formatted;
          }
          
          
          
          function videoDetails($url){
          $video_url = parse_url($url);
            if ($video_url['host'] == 'www.youtube.com' || 
                $video_url['host'] == 'youtube.com') {
                $videoid = youtube_id_from_url($url);
                $video_length = getVideolength($videoid);
                return $video_length;
            }else if ($video_url['host'] == 'www.youtu.be' || 
                      $image_url['host'] == 'youtu.be') {
                $videoid = youtube_id_from_url($url);
                $video_length = getVideolength($videoid);
                return $video_length;
            }else if ($video_url['host'] == 'www.vimeo.com' || 
                      $video_url['host'] == 'vimeo.com') {
                $oembed_endpoint = 'http://vimeo.com/api/oembed';
                $json_url = $oembed_endpoint.'.json?url='.
                            rawurlencode($video_url).'&width=640';
                $video_arr = curl_get($json_url);
                $video_arr = json_decode($video_arr, TRUE);
                $vid_duration = $video_arr['duration'];
                $video_length = 
                str_pad(floor($vid_duration / 60), 2, '0', STR_PAD_LEFT) . ':'
                .str_pad($vid_duration % 60, 2, '0', STR_PAD_LEFT);
                return $video_length;
             }
          }
}
