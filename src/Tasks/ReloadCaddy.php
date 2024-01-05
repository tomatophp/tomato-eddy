<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\Site;

class ReloadCaddy extends Task
{
    protected int $timeout = 30;

    public function __construct(
        protected Site $site
    ) {
    }

    /**
     * The command to run.
     */
    public function render(): string
    {
        return view('tomato-eddy::tasks.reload-caddy', [
            'site' => $this->site
        ]);
    }
}
