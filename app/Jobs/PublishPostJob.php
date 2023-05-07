<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Post;
use Exception;
use Log;
use Carbon\Carbon;

class PublishPostJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
            try {

                $publish_time = date('Y-m-d H:00:00',strtotime("-1 hour"));

                Post::where('publish_time', '<=', $publish_time)->chunk(100, function ($posts) {

                    foreach ($posts as $post) {

                        $post->is_published = YES;

                        $post->publish_time = date('Y-m-d H:i:s');

                        $post->save();

                    }
                });

                Log::info("Post Publish Success");

            } catch(Exception $e) {

                Log::info("Post Publish Error".print_r($e->getMessage(), true));

            }
        }
    }
