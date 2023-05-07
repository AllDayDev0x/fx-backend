<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        Commands\PublishPostCron::class,
        Commands\SubscriptionPaymentCron::class,
        Commands\PostFileRemoveCron::class,
        Commands\StopLiveVideoCron::class,
        Commands\StopLiveAudioCron::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

           $schedule->command('PublishPost:cron')->hourly();

           $schedule->command('SubscriptionPayment:cron')->daily();

           $schedule->command('PostFileRemove:cron')->hourly();

           $schedule->command('StopLiveVideo:cron')->everyMinute();

           $schedule->command('StopLiveAudio:cron')->everyMinute();

           $schedule->command('StopLiveStreaming:cron')->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
