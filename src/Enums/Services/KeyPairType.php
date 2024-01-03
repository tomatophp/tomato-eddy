<?php

namespace TomatoPHP\TomatoEddy\Enums\Services;

enum KeyPairType: string
{
    case Ed25519 = 'ed25519';
    case Rsa = 'rsa';
}
