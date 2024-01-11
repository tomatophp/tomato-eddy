<?php

namespace TomatoPHP\TomatoEddy\Tasks;

class InstallPhpMyAdmin extends Task
{
    /**
     * The command to run.
     */
    public function render(): string
    {
        return view('tomato-eddy::tasks.software.install-phpmyadmin');
    }
}
