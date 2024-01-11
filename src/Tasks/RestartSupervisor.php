<?php

namespace TomatoPHP\TomatoEddy\Tasks;

class RestartSuperVisor extends RestartService
{
    protected string $service = 'supervisor';
}
