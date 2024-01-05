<?php

namespace TomatoPHP\TomatoEddy\Infrastructure;


use Exception;
use ProtoneMedia\LaravelTaskRunner\ProcessRunner;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use TomatoPHP\TomatoEddy\Infrastructure\Interfaces\ServerProvider;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Server;

class ProviderFactory
{
    public function __construct(private ProcessRunner $processRunner)
    {
    }

    public function forServer(Server $server): ServerProvider
    {
        if ($server->credentials) {
            return $this->forCredentials($server->credentials);
        }

        return match ($server->provider) {
            Provider::Vagrant => new Vagrant($this->processRunner, config('tomato-eddy.vagrant.path')),

            default => throw new Exception('Invalid provider')
        };
    }

    public function forCredentials(Credentials $credentials): mixed
    {
        return match ($credentials->provider) {
            Provider::DigitalOcean => new DigitalOcean($credentials->credentials['digital_ocean_token']),
            Provider::HetznerCloud => new HetznerCloud($credentials->credentials['hetzner_cloud_token']),
            Provider::Vagrant => new Vagrant($this->processRunner, config('tomato-eddy.vagrant.path')),

            default => throw new Exception('Invalid provider')
        };
    }
}
