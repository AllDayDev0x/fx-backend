<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Log, Validator, Exception, DB, Setting;

use Illuminate\Http\Request;

use App\Models\PostFile;

class PostFileRemoveCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PostFileRemove:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'The unposted files will be deleted';

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
    public function handle(Request $request)
    {
        try {

            $current_time = \Carbon\Carbon::now()->subHours(3)->toDateTimeString();
                
            DB::beginTransaction();
            
            $post_files = PostFile::where('post_id',0)->where('created_at','<', $current_time)->get();

            // Don't remove the foreach (for file deletion from boot method)

            foreach($post_files as $post_file){

                $post_file->delete();
            }

            if($post_files){

                DB::commit();

            }

        } catch (Exception $e) {

            DB::rollback();

            Log::info("PostFileRemoveJob Error".print_r($e->getMessage(), true));
        
        }
    }
}
