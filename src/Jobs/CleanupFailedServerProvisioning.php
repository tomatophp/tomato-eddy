<?php

namespace TomatoPHP\TomatoEddy\Jobs;


use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Notifications\ServerProvisioningFailed;
use TomatoPHP\TomatoEddy\Tasks\Task;

class CleanupFailedServerProvisioning implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Server $server,
        public ?Task $task = null,
        public ?string $errorMessage = null,
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->server->provider_id) {
            $this->server->getProvider()->deleteServer($this->server->provider_id);
        }

        rescue(fn () => $this->task?->updateOutputWithoutCallbacks(), report: false);

        $this->server->createdByUser?->notify(
            new ServerProvisioningFailed(
                $this->server->name,
                $this->task?->tailOutput() ?: '',
                $this->errorMessage ?: ''
            )
        );

        $this->server->delete();
    }
}
