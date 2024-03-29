<?php

namespace TomatoPHP\TomatoEddy\Policies;

use TomatoPHP\TomatoEddy\Models\FirewallRule;
use App\Models\User;

class FirewallRulePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, FirewallRule $firewallRule): bool
    {
        return $user->currentTeam->id === $firewallRule->server->team_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, FirewallRule $firewallRule): bool
    {
        return $user->currentTeam->id === $firewallRule->server->team_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, FirewallRule $firewallRule): bool
    {
        return $user->currentTeam->id === $firewallRule->server->team_id;
    }
}
