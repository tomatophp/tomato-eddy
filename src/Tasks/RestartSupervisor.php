<?php

namespace TomatoPHP\TomatoEddy\Tasks;

class RestartSupervisor extends RestartService
{
    protected string $service = 'supervisor';
}
