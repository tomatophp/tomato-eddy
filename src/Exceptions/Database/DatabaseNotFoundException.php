<?php

namespace TomatoPHP\TomatoEddy\Exceptions\Database;

use TomatoPHP\TomatoEddy\Models\Server;
use Exception;
use ProtoneMedia\Splade\Facades\Toast;

class DatabaseNotFoundException extends Exception
{
    public function __construct(
        public Server $server,
        public $message
    ) {
        parent::__construct($message);
    }

    public function render()
    {
        Toast::warning(__("Database not found on server ':server'", [
            'server' => $this->server->name,
        ]));

        return back(fallback: route('servers.databases.index', $this->server));
    }
}
