<?php

namespace App\Jobs;

use App\KeyPairGenerator;
use App\Models\Deployment;
use App\Models\DeploymentStatus;
use App\Models\Server;
use App\Models\TlsSetting;
use App\Models\User;
use App\Services\Cloudflare;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Bus;

class CreateBulkSites implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public KeyPairGenerator $keyPairGenerator
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $servers = Server::with('sites')->get();
        foreach ($servers as $server) {
            if ($server->sites()->count() > 0) {
                foreach ($server->sites as $site) {
                    $site->load('server');
                    $site->load('deployments');
                    $deployment = $site->deployments()->create([
                        'status' => DeploymentStatus::Pending,
                        'user_id' => $this->user->id,
                    ]);

                    dispatch(new DeploySite($deployment));

                }
            } else {
                $deployKey = $this->keyPairGenerator->ed25519();

                $data = [
                    'address' => $server->name.'.3x1.one',
                    'php_version' => 'php82',
                    'type' => 'laravel',
                    'zero_downtime_deployment' => false,
                    'repository_url' => 'git@github.com:3x1io/bing-api.git',
                    'repository_branch' => 'master',
                    'deploy_key_uuid' => $deployKey->publicKey,
                    'web_folder' => '/public',
                ];

                //Link with Cloudflare
                $cloudflare = new Cloudflare();
                $cloudflare->create($server->name.'.3x1.one', $server->public_ipv4);

                $site = $server->sites()->make(Arr::except($data, 'deploy_key_uuid'));
                $site->tls_setting = TlsSetting::Auto;
                $site->user = $server->username;
                $site->path = "/home/{$site->user}/{$site->address}";
                $site->forceFill($site->type->defaultAttributes($site->zero_downtime_deployment));

                if ($data['deploy_key_uuid']) {
                    $site->deploy_key_public = $deployKey->publicKey;
                    $site->deploy_key_private = $deployKey->privateKey;
                }

                $site->save();

                if ($site) {
                    dispatch(new AddServerSshKeyToGithub($server, $this->user->githubCredentials->fresh()));
                    dispatch(new AttachStorageToServer($server, 250));
                    dispatch(new ResetServerFromInfrastructure($server, config('eddy.server_defaults.username'), 'Bingbing55'));

                    $database = $server->databases()->create([
                        'name' => $server->name,
                    ]);

                    $databaseUser = $database->users()->make([
                        'name' => $server->name,
                    ])->forceFill([
                        'server_id' => $server->id,
                    ]);

                    $databaseUser->save();
                    $databaseUser->databases()->attach($database);

                    Bus::chain([
                        new InstallDatabase($database, $this->user->fresh()),
                        new InstallDatabaseUser($databaseUser, '3x1@2023', $this->user->fresh()),
                    ])->dispatch();

                    $dataCron = [
                        'user' => $server->username,
                        'command' => 'php8.1 /home/eddy/'.$site->address.'/repository/artisan schedule:run',
                        'expression' => '* * * * *',
                    ];

                    $cron = $server->crons()->create($dataCron);
                    dispatch(new InstallCron($cron, $this->user));

//                    $dataDaemons = [
//                        'user' => $server->username,
//                        'processes' => 1,
//                        'stop_wait_seconds' => 10,
//                        'stop_signal' => 'TERM',
//                        'command' => 'php8.1 artisan queue:listen --timeout=0',
//                        'directory' => '/home/eddy/'.$site->address.'/repository',
//                    ];
//
//                    $daemon = $server->daemons()->create($dataDaemons);
//                    dispatch(new InstallDaemon($daemon, $this->user));

                    $site->deploy(user: $this->user);
                }
            }
        }
    }
}
