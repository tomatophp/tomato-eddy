<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\Server;

class AuthorizePublicKey extends Task
{
    protected int $timeout = 15;

    public function __construct(public Server $server, public string $publicKey, public bool $root = false)
    {
    }
}
