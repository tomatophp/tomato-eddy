<?php

namespace App\Tasks;

class ResetRootPassword extends Task
{
    protected int $timeout = 30;

    public function __construct(
        public string $username,
        public string $password,
    ) {
    }

    /**
     * The command to run.
     */
    public function render(): string
    {
        return 'echo '.$this->username.':'.$this->password.' | chpasswd';
    }
}
