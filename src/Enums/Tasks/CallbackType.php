<?php

namespace TomatoPHP\TomatoEddy\Enums\Tasks;

enum CallbackType: string
{
    case Custom = 'custom';
    case Timeout = 'timeout';
    case Failed = 'failed';
    case Finished = 'finished';
}
