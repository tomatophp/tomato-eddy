<?php

namespace App\Tasks;

use App\Models\Site;

class RunNodeBot extends Task
{
    public function __construct(
        public string $path,
    )
    {
    }

    public function render(): string
    {
        return view('tasks.node', [
            'path' => $this->path
        ]);
    }
}
