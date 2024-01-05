<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\Site;

class AptUpgrade extends Task
{

    /**
     * The command to run.
     */
    public function render(): string
    {
        return view('tomato-eddy::tasks.apt-upgrade');
    }
}
