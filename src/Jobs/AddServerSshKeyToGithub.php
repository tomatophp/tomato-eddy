<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\SourceControl\Github;
use TomatoPHP\TomatoEddy\SourceControl\ProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class AddServerSshKeyToGithub implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Server $server, public Credentials $githubCredentials)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ProviderFactory $providerFactory)
    {
        /** @var Github */
        $github = $providerFactory->forCredentials($this->githubCredentials);

        $appName = config('app.name');

        $github->addKey(
            "{$this->server->name} (added by {$appName})",
            $this->server->user_public_key
        );
    }
}
