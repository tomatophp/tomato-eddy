<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Tasks\DeleteFile;
use TomatoPHP\TomatoEddy\Tasks\UpdateCaddySiteImports;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UninstallSite implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Server $server, public $sitePath)
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->server->runTask(new UpdateCaddySiteImports($this->server))->throw()->asRoot()->dispatch();
        $this->server->runTask(new DeleteFile($this->sitePath))->throw()->asRoot()->dispatch();
    }
}
