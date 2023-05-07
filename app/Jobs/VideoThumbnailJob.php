<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use VideoThumbnail;

class VideoThumbnailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $thumbnail_data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($thumbnail_data)
    {
        $this->thumbnail_data = $thumbnail_data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $original_video_path = $this->thumbnail_data['original_video_path'];

        $save_file_path = $this->thumbnail_data['save_file_path'];

        $thumbnail_file_name = $this->thumbnail_data['thumbnail_file_name'];

        $original_image = $this->thumbnail_data['original_video_path'];

        VideoThumbnail::createThumbnail($original_video_path, $save_file_path, $thumbnail_file_name ,2);

    }
}
