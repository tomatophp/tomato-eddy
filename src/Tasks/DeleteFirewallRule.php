<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\FirewallRule;

class DeleteFirewallRule extends Task
{
    public function __construct(public FirewallRule $rule)
    {
    }
}
