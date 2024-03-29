<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus;
use TomatoPHP\TomatoEddy\Models\Server;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class WaitForServerToConnect implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Server $server)
    {
    }

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 60;

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        sleep(3);
        if (! $this->server->public_ipv4) {
            $ip = $this->server->getProvider()->getPublicIpv4OfServer($this->server->provider_id);

            if (! $ip) {
                $this->release(30);

                return false;
            }

            $this->server->forceFill(['public_ipv4' => $ip])->save();
        }

        if (! $this->server->canConnectOverSsh()) {
            $this->release(30);

            return false;
        }

        if ($this->server->status === ServerStatus::Starting) {
            $this->server->forceFill(['status' => ServerStatus::Running])->save();
        }

        return true;
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Throwable $exception)
    {
        dispatch(new CleanupFailedServerProvisioning($this->server));
    }
}
