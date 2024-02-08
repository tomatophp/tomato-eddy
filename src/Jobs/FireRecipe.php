<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Recipe;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\SourceControl\Github;
use TomatoPHP\TomatoEddy\SourceControl\ProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FireRecipe implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Server $server, public Recipe $recipe)
    {
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        if($this->recipe->user === 'root'){
            $this->server->runTask(new \TomatoPHP\TomatoEddy\Tasks\RunRecipe($this->recipe))
                ->asRoot()
                ->inBackground()
                ->keepTrackInBackground()
                ->updateLogIntervalInSeconds(10)
                ->dispatch();
        }
        else if($this->recipe->user === 'eddy'){
            $this->server->runTask(new \TomatoPHP\TomatoEddy\Tasks\RunRecipe($this->recipe))
                ->asUser()
                ->inBackground()
                ->keepTrackInBackground()
                ->updateLogIntervalInSeconds(10)
                ->dispatch();
        }
        else {
            $this->server->runTask(new \TomatoPHP\TomatoEddy\Tasks\RunRecipe($this->recipe))
                ->asUser($this->server->username)
                ->inBackground()
                ->keepTrackInBackground()
                ->updateLogIntervalInSeconds(10)
                ->dispatch();
        }

        $getLastTask = $this->server->tasks()->latest()->first();
        $saveToRecipeLog = $this->recipe->logs()->create([
            'server_id' => $this->server->id,
            'task_id' => $getLastTask->id
        ]);

    }
}
