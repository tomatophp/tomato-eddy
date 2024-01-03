<?php

namespace TomatoPHP\TomatoEddy\Infrastructure\Interfaces;

interface HasCredentials
{
    public function canConnect(): bool;
}
