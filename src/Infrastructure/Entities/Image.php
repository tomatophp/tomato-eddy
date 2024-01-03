<?php

namespace TomatoPHP\TomatoEddy\Infrastructure\Entities;

use TomatoPHP\TomatoEddy\Enums\Infrastructure\Distribution;
use TomatoPHP\TomatoEddy\Enums\Infrastructure\OperatingSystem;

class Image
{
    public function __construct(
        public readonly string $id,
        public readonly Distribution $distribution,
        public readonly OperatingSystem $operatingSystem,
    ) {
    }
}
