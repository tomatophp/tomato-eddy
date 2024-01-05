<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\Site;

class RunNodeBot extends Task
{
    public function __construct(
        public string $path,
    )
    {
    }

    public function render(): string
    {
        return view('tomato-eddy::tasks.node', [
            'path' => $this->path
        ]);
    }
}
