<?php

namespace App\Jobs;

use App\Models\Server;
use App\Tasks\AllowStorage;
use App\Tasks\FixChromeTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class FixChromeVersionServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Server $server
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->server->runTask(new FixChromeTask())->asRoot()->dispatch();
    }
}
