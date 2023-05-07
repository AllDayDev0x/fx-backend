<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;

use Log, Auth;

use Setting, Exception;

use App\Helpers\Helper;


class VideoWatermarkPositionJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    
    /**
      * The number of times the job may be attempted.
      *
      * @var int
      */
     public $tries = 2;
 
     /**
      * Create a new job instance.
      *
      * @return void
      */
     public function __construct($data)
     {
         $this->data = $data;
 
     }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        try {

        $video = $this->data['video'];

        $watermark_video = $this->data['watermark_video'];

        $watermark_path =  public_path("storage/".FILE_PATH_SITE.get_video_end(Setting::get('watermark_logo')));


        if(Setting::get('watermark_position') == WATERMARK_TOP_LEFT){
         // Top Left corner
         $top_left = "ffmpeg -i  ".$video." -i ".$watermark_path." -filter_complex ". '"\
         [1][0]scale2ref=h=ow/mdar:w=iw/8[#A video][image];\
         [#A video]format=argb,colorchannelmixer=aa=0.5[#B video transparent];\
         [image][#B video transparent]overlay\
         =(main_w-overlay_w)/(main_w-overlay_w):y=(main_h-overlay_h)/(main_h-overlay_h)"'." ".$watermark_video;
       
         exec($top_left);
        }

         else if(Setting::get('watermark_position') == WATERMARK_TOP_RIGHT){

         // Top Right corner
          $top_right = "ffmpeg -i   ".$video." -i ".$watermark_path." -filter_complex ". '"\
         [1][0]scale2ref=h=ow/mdar:w=iw/8[#A video][image];\
         [#A video]colorchannelmixer=aa=0.5[#B video transparent];\
         [image][#B video transparent]overlay\
         =(main_w-overlay_w):y=(main_h-overlay_h)/(main_h-overlay_h)"'." ".$watermark_video;
         exec($top_right);

         }
         else if(Setting::get('watermark_position') == WATERMARK_BOTTOM_RIGHT){
         $bottom_right = "ffmpeg -i  ".$video." -i ".$watermark_path." -filter_complex ". '"\
         [1][0]scale2ref=h=ow/mdar:w=iw/8[#A video][image];\
         [#A video]colorchannelmixer=aa=0.5[#B video transparent];\
         [image][#B video transparent]overlay\
         =(main_w-w)-(main_w*0.1):(main_h-h)-(main_h*0.1)"'." ".$watermark_video;
         exec($bottom_right);

         }

         else{
         // bottom Left corner
         $bottom_left = "ffmpeg -i  ".$video." -i ".$watermark_path." -filter_complex ". '"\
         [1][0]scale2ref=h=ow/mdar:w=iw/8[#A video][image];\
         [#A video]colorchannelmixer=aa=0.5[#B video transparent];\
         [image][#B video transparent]overlay\
         =(main_w-w)-(main_w*0.9):(main_h-h)-(main_h*0.1)"'." ".$watermark_video;
          exec($bottom_left);

         }
 

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }



    }
}
