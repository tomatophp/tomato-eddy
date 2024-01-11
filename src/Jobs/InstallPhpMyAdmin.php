<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Daemon;
use App\Models\User;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Tasks\ReloadSupervisor;
use TomatoPHP\TomatoEddy\View\Components\SupervisorProgram;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class InstallPhpMyAdmin implements ShouldQueue
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->server->runTask(InstallPhpMyAdmin::class)->asRoot()->dispatch();
    }

    /**
     * Handle a job failure.
     *
     * @return void
     */
    public function failed(Throwable $exception)
    {
        $this->daemon->forceFill(['installation_failed_at' => now()])->save();

        $this->daemon->server
            ->exceptionHandler()
            ->notify($this->user)
            ->about($exception)
            ->withReference(__("Installation of daemon ':daemon'", ['daemon' => "`{$this->daemon->command}`"]))
            ->send();
    }
}
