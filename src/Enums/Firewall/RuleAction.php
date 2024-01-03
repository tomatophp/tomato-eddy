<?php

namespace TomatoPHP\TomatoEddy\Enums\Firewall;

enum RuleAction: string
{
    case Allow = 'allow';
    case Deny = 'deny';
    case Reject = 'reject';
}
