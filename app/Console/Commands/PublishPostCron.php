<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\PublishPostJob;


class PublishPostCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'PublishPost:cron';

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
        \Log::info("Cron start!");

        PublishPostJob::dispatch();

        $this->info('PublishPost:cron Command Run successfully!');

    }
}
