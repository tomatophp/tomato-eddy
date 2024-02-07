<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Services\Cloudflare;
use TomatoPHP\TomatoEddy\SourceControl\Github;
use TomatoPHP\TomatoEddy\SourceControl\ProviderFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class LinkDomainToCloudflare implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(public Server $server, public string $address)
    {
    }

    /**
     * Execute the job.
     */
    public function handle(ProviderFactory $providerFactory)
    {
        //Link with Cloudflare
        $cloudflare = new Cloudflare();
        $cloudflare->create($address, $this->server->public_ipv4);
    }
}
