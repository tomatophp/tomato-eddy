<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\Site;

class UpdateCaddyfile extends Task
{
    protected int $timeout = 30;

    public function __construct(public Site $site, public string $caddyfile)
    {
    }
}
