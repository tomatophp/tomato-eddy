<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\Server;
use Illuminate\Support\LazyCollection;

class UpdateCaddySiteImports extends Task
{
    public function __construct(public Server $server)
    {
    }

    public function sites(): LazyCollection
    {
        return $this->server->sites()
            ->newQuery()
            ->select('path')
            ->whereNotNull('installed_at')
            ->lazy(chunkSize: 100);
    }
}
