<?php

namespace App\Console\Commands;

use App\Jobs\SubscriptionPaymentJob;

use Illuminate\Console\Command;

use Illuminate\Http\Request;


class SubscriptionPaymentCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'SubscriptionPayment:cron';

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
    public function handle(Request $request)
    {
        \Log::info("Payment Cron start!");

        SubscriptionPaymentJob::dispatch($request->all());

        $this->info('SubscriptionPayment:cron Command Run successfully!');
    }
}
