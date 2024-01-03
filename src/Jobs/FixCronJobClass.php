<?php

namespace App\Jobs;

use App\Infrastructure\Entities\Server;
use App\Models\Cron;
use App\Models\Site;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FixCronJobClass implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public ?string $serverID=null
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {


    }
}
