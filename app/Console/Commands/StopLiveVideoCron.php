<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Helpers\Helper;

use Log, Validator, Exception, DB, Setting;

use App\Models\VideoCallRequest;

class StopLiveVideoCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'StopLiveVideo:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Time expired video calls will be deleted automatically';

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

            $current_time = \Carbon\Carbon::now()->subHours(3)->toDateTimeString();
                
            DB::beginTransaction();
            
            VideoCallRequest::where('start_time','<', $current_time)->whereIn('call_status',[VIDEO_CALL_REQUEST_JOINED,VIDEO_CALL_REQUEST_ACCEPTED,VIDEO_CALL_REQUEST_SENT])->update(['call_status'=>VIDEO_CALL_REQUEST_ENDED, 'end_time'=>\Carbon\Carbon::now()->toDateTimeString(),'message'=>tr('timeout')]);

            DB::commit();

        } catch (Exception $e) {

            DB::rollback();

            Log::info("StopLiveVideoCron Error".print_r($e->getMessage(), true));
        
        }
    }
}
