<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\Helper;

use Log, Validator, Exception, DB, Setting;

use App\Models\LiveVideo;

class StopLiveStreaming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StopLiveStreaming:cron';
 
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {

            $sub_hours = now()->subHours(3);
                
            DB::beginTransaction();
            
            $ongoing_live_videos =  LiveVideo::where('created_at', '<', $sub_hours)
                                    ->where(['is_streaming' => IS_STREAMING_YES, 'status' => VIDEO_STREAMING_ONGOING])->get();

            foreach($ongoing_live_videos as $ongoing_live_video) {

                $ongoing_live_video->update([
                    'is_streaming' => IS_STREAMING_NO,
                    'end_time'=> now()->format("H:i:s"),
                    'no_of_minutes' => getMinutesBetweenTime($ongoing_live_video->start_time, now()->format("H:i:s")),
                    'status' => VIDEO_STREAMING_STOPPED, 
                ]);
            }

            DB::commit();

        } catch (Exception $e) {

            DB::rollback();

            Log::info("StopLiveStreaming Error : ".print_r($e->getMessage(), true));
        
        }
    }
}
