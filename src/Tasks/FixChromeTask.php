<?php

namespace App\Tasks;

class FixChromeTask extends Task
{
    protected int $timeout = 30;

    public function __construct(
    ) {
    }

    /**
     * The command to run.
     */
    public function render(): string
    {
        return view('tasks.fix-chrome');
    }
}
