<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Server;
use App\Models\User;
use TomatoPHP\TomatoEddy\Notifications\ServerStopFailed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class StopServerFromInfrastructure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server, public User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->server->provider_id) {
            $this->server->getProvider()->stopServer($this->server->provider_id);
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(): void
    {
        $this->user->notify(new ServerStopFailed($this->server->name));
    }
}
