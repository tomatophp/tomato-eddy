<?php

namespace TomatoPHP\TomatoEddy\Models;

use App\Models\User;
use TomatoPHP\TomatoAdmin\Models\Team;
use TomatoPHP\TomatoEddy\Events\ServerDeleted;
use TomatoPHP\TomatoEddy\Events\ServerUpdated;
use TomatoPHP\TomatoEddy\Exceptions\ServerHandler;
use TomatoPHP\TomatoEddy\Enums\Infrastructure\ServerStatus;
use TomatoPHP\TomatoEddy\Infrastructure\Entities\ServerType;
use TomatoPHP\TomatoEddy\Infrastructure\Interfaces\ServerProvider;
use TomatoPHP\TomatoEddy\Infrastructure\ProviderFactory;
use TomatoPHP\TomatoEddy\Jobs\AddServerSshKeyToGithub;
use TomatoPHP\TomatoEddy\Jobs\CreateServerOnInfrastructure;
use TomatoPHP\TomatoEddy\Jobs\ProvisionServer;
use TomatoPHP\TomatoEddy\Jobs\WaitForServerToConnect;
use TomatoPHP\TomatoEddy\Models\Task as TaskModel;
use TomatoPHP\TomatoEddy\Enums\Services\Provider;
use TomatoPHP\TomatoEddy\Server\Database\Interfaces\DatabaseManager;
use TomatoPHP\TomatoEddy\Server\Database\MySqlDatabase;
use TomatoPHP\TomatoEddy\Enums\Server\PhpVersion;
use TomatoPHP\TomatoEddy\Server\ServerFiles;
use TomatoPHP\TomatoEddy\Enums\Server\Software;
use TomatoPHP\TomatoEddy\Tasks\Task;
use TomatoPHP\TomatoEddy\Tasks\UploadFile;
use TomatoPHP\TomatoEddy\Tasks\Whoami;
use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\HtmlString;
use ProtoneMedia\LaravelTaskRunner\Connection;
use ProtoneMedia\LaravelTaskRunner\PendingTask;

/**
 * @property EloquentCollection $crons
 * @property EloquentCollection $daemons
 * @property EloquentCollection $firewallRules
 * @property Team $team
 * @property Credentials|null $credentials
 * @property User|null $createdByUser
 */
class Server extends Model
{
    use HasFactory;
    use HasUlids;

    protected $fillable = [
        'name',
        'credentials_id',
        'region',
        'type',
        'image',
        'storage_id',
        'storage_name',
        'description',
        'is_ready',
        'ssk_key_id',
        'provider_id',
        'storage_id'
    ];

    protected $casts = [
        'completed_provision_steps' => AsArrayObject::class,
        'cpu_cores' => 'integer',
        'database_password' => 'encrypted',
        'installed_software' => AsArrayObject::class,
        'memory_in_mb' => 'integer',
        'password' => 'encrypted',
        'private_key' => 'encrypted',
        'provider' => Provider::class,
        'provisioned_at' => 'datetime',
        'public_key' => 'encrypted',
        'ssh_port' => 'integer',
        'status' => ServerStatus::class,
        'storage_in_gb' => 'integer',
        'uninstallation_requested_at' => 'datetime',
        'user_public_key' => 'encrypted',
        'provider_id' => 'encrypted',
    ];

    protected $dispatchesEvents = [
        'updated' => ServerUpdated::class,
    ];

    protected static function booted()
    {
        static::creating(function (Server $server) {
            $server->status ??= ServerStatus::New;
            $server->completed_provision_steps ??= [];
            $server->installed_software ??= [];
        });

        static::deleted(function ($server) {
            event(new ServerDeleted($server->id, $server->team_id));
        });
    }

    /**
     * Returns the name with the IP address appended.
     */
    public function getNameWithIpAttribute(): string
    {
        return "{$this->name} ({$this->public_ipv4})";
    }

    /**
     * Returns the name of the provider.
     */
    public function getProviderNameAttribute(): string
    {
        return $this->provider->name;
    }

    /**
     * Returns the formatted status of the server.
     */
    public function getStatusNameAttribute(): string
    {
        return $this->status->name;
    }

    /**
     * Returns a Collection of Software enums that are installed on the server.
     */
    public function installedSoftware(): Collection
    {
        return $this->installed_software->collect()->map(function ($software) {
            return Software::from($software);
        });
    }

    /**
     * Returns a boolean indicating if the server has the given software installed.
     */
    public function softwareIsInstalled(Software $software): bool
    {
        return $this->installedSoftware()->contains($software);
    }

    /**
     * Returns a key-value Collection of installed PHP versions.
     */
    public function installedPhpVersions(): array
    {
        return $this->installed_software->collect()->map(function ($software) {
            return Software::from($software)->findPhpVersion();
        })->filter()->pipe(function ($phpVersions) {
            return PhpVersion::named($phpVersions->all());
        });
    }

    /**
     * Returns an instance of the Infrastructure Provider.
     */
    public function getProvider(): ServerProvider
    {
        return app(ProviderFactory::class)->forServer($this);
    }

    /**
     * Updates the model by the ServerType entity.
     */
    public function updateType(ServerType $type): self
    {
        $this->forceFill([
            'cpu_cores' => $type->cpuCores,
            'memory_in_mb' => $type->memoryInMb,
            'storage_in_gb' => $type->storageInGb,
            'type' => $type->id,
        ])->save();

        return $this;
    }

    /**
     * Returns a signed URL that containts the script to connect a custom server to the app.
     */
    public function provisionScriptUrl(): string
    {
        $host = rtrim(config('tomato-eddy.webhook_url') ?: config('app.url'), '/');

        return $host.URL::signedRoute(
            name: 'servers.provisionScript',
            parameters: ['server' => $this],
            absolute: false
        );
    }

    /**
     * A bash provision command used to start the provision of custom servers.
     */
    public function provisionCommand(): HtmlString
    {
        return new HtmlString("wget --no-verbose -O - {$this->provisionScriptUrl()} | bash");
    }

    /**
     * Returns a boolean indicating if we can connect to the server.
     */
    public function canConnectOverSsh(): bool
    {
        if (! $this->public_ipv4) {
            return false;
        }

        try {
            $result = $this->runTask(Whoami::class)->asRoot()->dispatch();
        } catch (CouldNotConnectToServerException $e) {
            return false;
        }

        return $result && $result->isSuccessful() && $result->getBuffer() === 'root';
    }

    /**
     * Returns an instance of the TaskDispatcher with the given task and this server as target.
     */
    public function runTask(string|Task|PendingTask $task): ServerTaskDispatcher
    {
        return new ServerTaskDispatcher($this, PendingTask::make($task));
    }

    /**
     * Returns a random IP address from the configuration that can be
     * used as a SSH proxy.
     */
    private static function randomSshProxy(): ?string
    {
        $proxies = array_filter(config('tomato-eddy.ssh_proxies', []));

        return count($proxies) > 0 ? Arr::random($proxies) : null;
    }

    /**
     * Returns a Connection instance to connect to the server as root.
     */
    public function connectionAsRoot(): Connection
    {
        return new Connection(
            host: $this->public_ipv4,
            port: $this->ssh_port,
            username: 'root',
            privateKey: $this->private_key,
            scriptPath: "/root/{$this->working_directory}",
            proxyJump: self::randomSshProxy()
        );
    }

    /**
     * Returns a Connection instance to connect to the server as a non-root user.
     */
    public function connectionAsUser(string $username = null): Connection
    {
        $username = $username ?? $this->username;

        return new Connection(
            host: $this->public_ipv4,
            port: $this->ssh_port,
            username: $username,
            privateKey: $this->private_key,
            scriptPath: "/home/{$username}/{$this->working_directory}",
            proxyJump: self::randomSshProxy()
        );
    }

    /**
     * Dispatches a chain of jobs to provision the server.
     */
    public function dispatchCreateAndProvisionJobs(Collection $sshKeys, Credentials $addSshKeyToGithub = null): void
    {
        $server = $this->fresh();

        $jobs = [
            new CreateServerOnInfrastructure($server),
            new WaitForServerToConnect($server),
            new ProvisionServer($server, EloquentCollection::make($sshKeys)),
        ];

        if ($addSshKeyToGithub && $addSshKeyToGithub->exists) {
            $jobs[] = new AddServerSshKeyToGithub($server, $addSshKeyToGithub->fresh());
        }

        Bus::chain($jobs)->dispatch();
    }

    /**
     * Uploads the given file to the server as root.
     */
    public function uploadAsRoot(string $path, string $contents, bool $throw = false): bool
    {
        $task = new UploadFile($path, $contents);

        return $this->runTask($task)->asRoot()->throw($throw)->dispatch()->isSuccessful();
    }

    /**
     * Uploads the given file to the server as a non-root user.
     */
    public function uploadAsUser(string $path, string $contents, string $username = null, bool $throw = false): bool
    {
        $task = new UploadFile($path, $contents);

        return $this->runTask($task)->asUser($username)->throw($throw)->dispatch()->isSuccessful();
    }

    /**
     * Returns an instance of the DatabaseManager for this server.
     */
    public function databaseManager(): DatabaseManager
    {
        return app()->makeWith(MySqlDatabase::class, ['server' => $this]);
    }

    /**
     * Returns an instance of ServerFiles to manage files on this server.
     */
    public function files(): ServerFiles
    {
        return new ServerFiles($this);
    }

    /**
     * Returns an exception handler for this server.
     */
    public function exceptionHandler(): ServerHandler
    {
        return new ServerHandler($this);
    }

    //

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function credentials(): BelongsTo
    {
        return $this->belongsTo(Credentials::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(TaskModel::class);
    }

    public function sites(): HasMany
    {
        return $this->hasMany(Site::class);
    }

    public function crons(): HasMany
    {
        return $this->hasMany(Cron::class);
    }

    public function daemons(): HasMany
    {
        return $this->hasMany(Daemon::class);
    }

    public function databases(): HasMany
    {
        return $this->hasMany(Database::class)
            ->orderBy((new Database)->qualifyColumn('name'));
    }

    public function databaseUsers(): HasMany
    {
        return $this->hasMany(DatabaseUser::class)
            ->orderBy((new DatabaseUser)->qualifyColumn('name'));
    }

    public function firewallRules(): HasMany
    {
        return $this->hasMany(FirewallRule::class);
    }
}
