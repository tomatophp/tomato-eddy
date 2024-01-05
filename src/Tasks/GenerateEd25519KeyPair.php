<?php

namespace TomatoPHP\TomatoEddy\Tasks;

class GenerateEd25519KeyPair extends Task
{
    public function __construct(public string $privatePath)
    {
    }

    public function comment()
    {
        return config('tomato-eddy.server_defaults.ssh_comment');
    }
}
