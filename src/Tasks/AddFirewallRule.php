<?php

namespace TomatoPHP\TomatoEddy\Tasks;

use TomatoPHP\TomatoEddy\Models\FirewallRule;

class AddFirewallRule extends Task
{
    public function __construct(public FirewallRule $rule)
    {
    }
}
