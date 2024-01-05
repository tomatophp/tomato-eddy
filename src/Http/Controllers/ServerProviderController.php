<?php

namespace TomatoPHP\TomatoEddy\Http\Controllers;

use TomatoPHP\TomatoEddy\Infrastructure\Entities\Image;
use TomatoPHP\TomatoEddy\Enums\Infrastructure\OperatingSystem;
use TomatoPHP\TomatoEddy\Infrastructure\Entities\Region;
use TomatoPHP\TomatoEddy\Infrastructure\Entities\ServerType;
use TomatoPHP\TomatoEddy\Infrastructure\ProviderFactory;
use TomatoPHP\TomatoEddy\Models\Credentials;

/**
 * @codeCoverageIgnore Handled by Dusk tests.
 */
class ServerProviderController extends Controller
{
    public function __construct(private ProviderFactory $providerFactory)
    {
    }

    public function regions(Credentials $credentials)
    {
        $provider = $this->providerFactory->forCredentials($credentials);

        return $provider->findAvailableServerRegions()->mapWithKeys(function (Region $region) {
            return [$region->id => $region->name];
        });
    }

    public function types(Credentials $credentials, $region)
    {
        $provider = $this->providerFactory->forCredentials($credentials);

        return $provider->findAvailableServerTypesByRegion($region)
            ->sortBy(function (ServerType $serverType) {
                return $serverType->monthlyPriceAmount;
            })
            ->mapWithKeys(function (ServerType $serverType) {
                return [$serverType->id => $serverType->name];
            });
    }

    public function images(Credentials $credentials, $region)
    {
        $provider = $this->providerFactory->forCredentials($credentials);

        return $provider->findAvailableServerImagesByRegion($region)
            ->filter(function (Image $image) {
                return $image->operatingSystem === OperatingSystem::Ubuntu2204;
            })
            ->mapWithKeys(function (Image $image) {
                return [$image->id => 'Ubuntu 22.04'];
            });
    }
}
