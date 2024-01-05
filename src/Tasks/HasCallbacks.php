<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\Task;
use Illuminate\Http\Request;

interface HasCallbacks
{
    public function handleCallback(Task $task, Request $request, CallbackType $type);
}
