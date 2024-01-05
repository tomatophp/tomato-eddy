<?php

namespace TomatoPHP\TomatoEddy\Services;

use TomatoPHP\TomatoEddy\Enums\Services\KeyPairType;

class KeyPair
{
    public function __construct(
        public readonly string $privateKey,
        public readonly string $publicKey,
        public readonly KeyPairType $type
    ) {
    }
}
