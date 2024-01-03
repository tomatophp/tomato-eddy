<?php

namespace TomatoPHP\TomatoEddy\Infrastructure\Interfaces;

use TomatoPHP\TomatoEddy\Infrastructure\Entities;
use Illuminate\Support\Collection;

interface ServerProvider
{
    public function findAvailableServerRegions(): Collection;

    public function findAvailableServerTypesByRegion(string $regionId): Collection;

    public function findAvailableServerImagesByRegion(string $regionId): Collection;

    public function findSshKeyByPublicKey(string $publicKey): ?Entities\SshKey;

    public function createSshKey(string $publicKey): Entities\SshKey;

    public function createServer(string $name, string $regionId, string $typeId, string $imageId, array|string|Collection $sshKeyIds): string;

    public function getServer(string $id): Entities\Server;

    public function deleteServer(string $id): void;

    public function getPublicIpv4OfServer(string $id): ?string;
}
