<?php

namespace TomatoPHP\TomatoEddy\Models;

use Illuminate\Database\Eloquent\Model;
use TomatoPHP\TomatoEddy\TeamSubscriptionOptions;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;
use Spark\Billable;

/**
 * @property Collection<int, Server> $servers
 */
class Team extends Model
{
    use HasFactory;
    use HasUlids;

    protected $casts = [
        'personal_team' => 'boolean',
        'requires_subscription' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'personal_team',
    ];

    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    public function servers(): HasMany
    {
        return $this->hasMany(Server::class)->orderBy(
            (new Server)->qualifyColumn('name')
        );
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Returns an instanceof TeamSubscriptionOptions to determine
     * what this team can do and what it can't.
     */
    public function subscriptionOptions(): TeamSubscriptionOptions
    {
        return app()->makeWith(TeamSubscriptionOptions::class, ['team' => $this]);
    }

    /**
     * Get the email address that should be associated with the Paddle customer.
     */
    public function paddleEmail(): ?string
    {
        /** @var User */
        $owner = $this->owner;

        return $owner->email;
    }
}
