<?php

namespace TomatoPHP\TomatoEddy\SourceControl;

use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use Exception;

class ProviderFactory
{
    public function forCredentials(Credentials $credentials): mixed
    {
        return match ($credentials->provider) {
            Provider::Github => new Github($credentials->credentials['token']),

            default => throw new Exception('Invalid provider')
        };
    }
}
