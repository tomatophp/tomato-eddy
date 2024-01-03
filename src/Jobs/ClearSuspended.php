<?php

namespace App\Jobs;

use App\Models\Server;
use App\Models\Site;
use App\Services\BingAPI;
use App\Services\Cloudflare;
use App\Services\Forge;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearSuspended implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Forge $forge;

    protected Cloudflare $cloudflare;

    protected GeneralSettings $settings;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->forge = new Forge();
        $this->cloudflare = new Cloudflare();
        $this->settings = new GeneralSettings();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->forge->servers();
        $servers = Server::all();
        foreach ($servers as $item) {
            try {
                $this->forge->sites($item->forge_server_id);
            } catch (\Exception $e) {
                continue;
            }

        }
        $sites = Site::all();

        foreach ($sites as $site) {
            try {
                $bing = new BingAPI('https://'.$site->name.'/api');
                $bing->clearSuspended();
            } catch (\Exception $e) {
                continue;
            }
        }
    }
}
