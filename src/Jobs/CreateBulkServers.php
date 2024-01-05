<?php

namespace TomatoPHP\TomatoEddy\Jobs;

use TomatoPHP\TomatoEddy\Services\KeyPairGenerator;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\SshKey;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateBulkServers implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $count,
        public Credentials $credentials,
        public string $region,
        public string $type,
        public string $image,
        public User $user,
        public ?string $public_ipv4,
        public ?array $ssh_keys,
        public ?KeyPairGenerator $keyPairGenerator
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        for ($i = 0; $i < $this->count; $i++) {
            $server = $this->user->currentTeam->servers()->make([
                'name' => 'bing'.explode('-', Str::uuid())[0],
                'credentials_id' => $this->credentials?->id,
                'region' => $this->region,
                'type' => $this->type,
                'image' => $this->image,
            ]);

            $keyPair = $this->keyPairGenerator->ed25519();
            $server->public_key = $keyPair->publicKey;
            $server->private_key = $keyPair->privateKey;

            $server->working_directory = config('eddy.server_defaults.working_directory');
            $server->ssh_port = config('eddy.server_defaults.ssh_port');
            $server->username = config('eddy.server_defaults.username');

            $server->password = Str::password(symbols: false);
            $server->database_password = Str::password(symbols: false);

            $server->public_ipv4 = $this->public_ipv4;
            $server->provider = $this->credentials->provider;
            $server->created_by_user_id = $this->user->id;

            $server->save();
            $server->dispatchCreateAndProvisionJobs(
                SshKey::whereKey($this->ssh_keys)->get(),
                $this->user->githubCredentials,
            );
        }
    }
}
