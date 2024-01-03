<?php

namespace App\Tasks;

use App\Models\Site;

class FixDeployTask extends Task
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
        return view('tasks.fix-deploy', [
            'site' => $this->site
        ]);
    }
}
