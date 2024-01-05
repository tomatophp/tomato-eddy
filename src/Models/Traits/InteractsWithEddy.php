<?php

namespace TomatoPHP\TomatoEddy\Models\Traits;

use Illuminate\Database\Eloquent\Concerns\HasUlids;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\SshKey;

trait InteractsWithEddy
{

    public function getInitialsAttribute()
    {
        $name = $this->name;
        $initials = '';

        $parts = array_filter(explode(' ', $name));

        foreach ($parts as $word) {
            $initials .= $word[0];
        }

        return strtoupper($initials);
    }

    public function sshKeys()
    {
        return $this->hasMany(SshKey::class)->orderBy(
            (new SshKey)->qualifyColumn('name')
        );
    }

    public function credentials()
    {
        return $this->hasMany(Credentials::class)->orderBy(
            (new Credentials)->qualifyColumn('name')
        );
    }

    public function githubCredentials()
    {
        return $this->credentials()->one()->where('provider', Provider::Github);
    }

    /**
     * Returns a boolean whether this user has a Github credentials.
     */
    public function hasGithubCredentials(): bool
    {
        return $this->githubCredentials()->exists();
    }
}
