<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Log, Setting, Exception, Storage, File, Image;

use App\Helpers\Helper;

use App\Models\PostFile;

class PostBlurFile implements ShouldQueue
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
        try {

            $post_file_id = $this->data['post_file_id'];

            $post_file = PostFile::find($post_file_id);

            File::makeDirectory(Storage::path('public/'.POST_BLUR_PATH.$post_file->user_id), 0777, true, true);

            $storage_file_path = 'public/'.POST_PATH.$post_file->user_id.'/'.basename($post_file->file);

            $output_file_path = 'public/'.POST_BLUR_PATH.$post_file->user_id.'/'.basename($post_file->file);

            Storage::copy($storage_file_path, $output_file_path);

            $img = Image::make(Storage::path($output_file_path));

            $img->blur(100)->save(Storage::path($output_file_path));
            
            generate_blur_file(Storage::path($output_file_path));

            $post_file->blur_file = asset(Storage::url($output_file_path));

            $post_file->save();

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }

    }
}
