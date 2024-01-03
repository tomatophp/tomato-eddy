<?php

namespace App\Jobs;

use App\Services\Forge;
use App\Settings\GeneralSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class CreateServerJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public GeneralSettings $settings;

    public Forge $forge;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $id = Str::lower(Str::random(10));
        $this->forge->createServer([
            'provider' => $this->settings->server_provider,
            'credential_id' => config('services.forge.credential'),
            'name' => 'bing'.$id,
            'type' => $this->settings->server_type,
            'size' => $this->settings->server_plan,
            'database' => 'bing'.$id,
            'php_version' => $this->settings->server_php_version,
            'region' => $this->settings->server_location,
            'recipe_id' => 47844,
            'database_type' => $this->settings->server_database_type,
            'maria' => true,
        ]);
    }
}
