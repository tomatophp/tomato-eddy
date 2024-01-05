<?php

namespace TomatoPHP\TomatoEddy\Tasks;

class DeleteFile extends Task
{
    protected int $timeout = 30;

    public function __construct(
        public string $path,
    ) {
    }
}
