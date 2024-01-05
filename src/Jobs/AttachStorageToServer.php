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

class AttachStorageToServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server, public int $size)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->server->provider_id) {
            $storage = $this->server->getProvider()->createServerVolume($this->server->provider_id, 'volume-'.rand(999, 99999), $this->size);
            $this->server->storage_id = $storage['id'];
            $this->server->storage_name = Str::replace('/dev/disk/by-id/scsi-0', '', $storage['linux_device']);
            $this->server->save();
            $this->server->runTask(new AllowStorage($this->server->storage_name))->asRoot()->dispatch();
        }
    }
}
