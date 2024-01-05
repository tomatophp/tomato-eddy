<?php

namespace TomatoPHP\TomatoEddy\Tasks;

class AllowStorage extends Task
{
    protected int $timeout = 30;

    public function __construct(
        public string $storage
    ) {
    }

    /**
     * The command to run.
     */
    public function render(): string
    {
        return 'chmod 777 /mnt/'.$this->storage;
    }
}
