<?php

namespace TomatoPHP\TomatoEddy\Exceptions\Database;

use TomatoPHP\TomatoEddy\Models\Server;
use Exception;
use ProtoneMedia\Splade\Facades\Toast;

class CouldNotCreateUserException extends Exception
{
    public function __construct(
        public Server $server,
        public $message
    ) {
        parent::__construct($message);
    }

    public function render()
    {
        Toast::warning(__("Could not create the user on server ':server'", [
            'server' => $this->server->name,
        ]));

        return back(fallback: route('servers.databases.index', $this->server));
    }
}
