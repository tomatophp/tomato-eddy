<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Tasks\AllowStorage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class DeleteStorageFromServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->server->provider_id) {
            $action = $this->server->getProvider()->detachVolume($this->server->storage_id);
            if($action){
                $this->server->getProvider()->destroyVolume($this->server->storage_id);

                $this->server->storage_id = null;
                $this->server->storage_name = null;
                $this->server->save();
            }
        }
    }
}
