<?php

namespace TomatoPHP\TomatoEddy\Enums\Models;

enum TaskStatus: string
{
    case Finished = 'finished';
    case Pending = 'pending';
    case Timeout = 'timeout';
    case UploadFailed = 'upload_failed';
    case Failed = 'failed';
}
