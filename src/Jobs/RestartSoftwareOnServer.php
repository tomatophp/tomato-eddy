<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Enums\Server\Software;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class RestartSoftwareOnServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server, public Software $software)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $taskClass = $this->software->restartTaskClass();

        if (! $taskClass) {
            return;
        }

        $this->server->runTask($taskClass)
            ->asRoot()
            ->inBackground()
            ->throw()
            ->dispatch();
    }
}
