<?php

namespace TomatoPHP\TomatoEddy\Infrastructure\Entities;

class Region
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
    ) {
    }
}
