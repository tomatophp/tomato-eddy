<?php

namespace App\Tasks;

use App\Models\Site;

class AptUpgrade extends Task
{

    /**
     * The command to run.
     */
    public function render(): string
    {
        return view('tasks.apt-upgrade');
    }
}
