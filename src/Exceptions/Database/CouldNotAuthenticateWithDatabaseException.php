<?php

namespace TomatoPHP\TomatoEddy\Exceptions\Database;

use TomatoPHP\TomatoEddy\Models\Server;
use Exception;
use ProtoneMedia\Splade\Facades\Toast;

class CouldNotAuthenticateWithDatabaseException extends Exception
{
    public function __construct(
        public Server $server,
        public $message
    ) {
        parent::__construct($message);
    }

    public function render()
    {
        Toast::warning(__("Could not authenticate with the database on server ':server'", [
            'server' => $this->server->name,
        ]));

        return back(fallback: route('servers.show', $this->server));
    }
}
