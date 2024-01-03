<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\Site;
use App\Services\Cloudflare;
use App\Services\Forge;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateSiteJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public GeneralSettings $settings;

    public Forge $forge;

    public Cloudflare $cloudflare;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public string $server_id,
    ) {
        $this->cloudflare = new Cloudflare();
        $this->settings = new GeneralSettings();
        $this->forge = new Forge();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $setId = Str::lower(Str::random(10));
        $getServer = Server::where('forge_server_id', $this->server_id)->first();
        $domain = 'bing'.$setId.'.'.$this->settings->domain;

        //Add DNS TO CLOUDFLARE
        $cloudFlareId = $this->cloudflare->create($domain, $getServer->ip_address);
        $site = $this->forge->createSite([
            'domain' => $domain,
            'project_type' => 'php',
            'directory' => '/public',
            'php_version' => $this->settings->server_php_version,
        ], $getServer->forge_server_id);
        sleep(5);

        $ssl = $this->forge->createSSL([
            'domains' => [$domain],
            'dns_provider' => [
                'type' => 'cloudflare',
                'cloudflare_api_token' => config('services.cloudflare.api_token'),
            ],
        ], $this->server_id, $site->id);
        $database = $this->forge->createDatabase([
            'name' => 'bing'.$setId,
            'user' => 'bing'.$setId,
            'password' => $this->settings->server_database_password,
        ], $this->server_id);
        $github = $this->forge->installGithub([
            'provider' => 'github',
            'repository' => '3x1io/bing',
            'branch' => 'main',
            'composer' => true,
        ], $this->server_id, $site->id);
        sleep(30);
        $env = $this->forge->updateEnv([
            'content' => '
APP_NAME=bing
APP_ENV=local
APP_KEY=
APP_DEBUG=false
APP_URL=https://'.$domain.'
APP_DOMAIN='.$domain.'
CWEBP_PATH=/opt/homebrew/Cellar/webp/1.2.0/bin/cwebp
DEVELOPER_GATE=true
COOKIES_PATH=/mnt/

LOG_CHANNEL=stack
LOG_LEVEL=debug

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=bing'.$setId.'
DB_USERNAME=bing'.$setId.'
DB_PASSWORD='.$this->settings->server_database_password.'

BROADCAST_DRIVER=pusher
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=null
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

MESSAGEBIRD_ACCESS_KEY=
MESSAGEBIRD_ORIGINATOR=
MESSAGEBIRD_RECIPIENTS=

MC_KEY=

GITHUB_TOKEN=

PAYTABS_EMAIL=
PAYTABS_KEY=
',
        ], $this->server_id, $site->id);
        $this->forge->runCommand([
            'command' => 'php artisan key:generate',
        ], $this->server_id, $site->id);
        $this->forge->runCommand([
            'command' => 'php artisan config:cache',
        ], $this->server_id, $site->id);

        $this->forge->updateDeployScript([
            'content' => '
cd /home/forge/'.$domain.'
git pull origin $FORGE_SITE_BRANCH
$FORGE_COMPOSER install --no-interaction --prefer-dist --optimize-autoloader

( flock -w 10 9 || exit 1
    echo \'Restarting FPM...\'; sudo -S service $FORGE_PHP_FPM reload ) 9>/tmp/fpmlock

if [ -f artisan ]; then
    $FORGE_PHP artisan migrate --force
    $FORGE_PHP artisan dusk:install
fi
            ',
        ], $this->server_id, $site->id);

        $this->forge->deploySite($this->server_id, $site->id);

        $checkIfSiteExist = Site::where('server_id', $this->server_id)->where('name', $domain)->first();
        if (! $checkIfSiteExist) {
            $getServerId = Server::where('forge_server_id', $this->server_id)->first();
            $newSite = new Site();
            $newSite->cloudflare_id = $cloudFlareId;
            $newSite->name = $domain;
            $newSite->server_id = $getServerId->id;
            $newSite->forge_server_id = $this->server_id;
            $newSite->forge_site_id = $site->id;
            $newSite->php_version = $site->php_version;
            $newSite->server_created_at = $site->created_at;
            $newSite->save();
        }
    }
}
