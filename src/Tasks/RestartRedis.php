<?php

namespace TomatoPHP\TomatoEddy\Tasks;

class RestartRedis extends RestartService
{
    protected string $service = 'redis-server';
}
