<?php

namespace App\Jobs;

use App\Models\Server;
use App\Notifications\ServerResetFailed;
use App\Tasks\ResetRootPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ResetServerFromInfrastructure implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Server $server, public string $username, public string $password)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $this->server->runTask(new ResetRootPassword($this->username, $this->password))
            ->asRoot()
            ->dispatch();

        $this->server->password = $this->password;
        $this->server->save();
    }

    /**
     * The job failed to process.
     */
    public function failed(): void
    {
        $this->user->notify(new ServerResetFailed($this->server->name));
    }
}
