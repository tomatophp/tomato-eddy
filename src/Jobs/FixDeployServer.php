<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Server;
use App\Models\Site;
use App\Models\TlsSetting;
use App\Services\BingAPI;
use App\Tasks\AllowStorage;
use App\Tasks\AptUpgrade;
use App\Tasks\FixChromeTask;
use App\Tasks\FixDeployTask;
use App\Tasks\ReloadCaddy;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class FixDeployServer implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Server|Collection $server
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        if($this->server instanceof Collection && count($this->server)){
            foreach ($this->server as $server){
                $site = Site::where('server_id', $server->id)->firstOrFail();
                $server->runTask(new AptUpgrade())->asRoot()->dispatch();
                $site->tls_setting = TlsSetting::Auto;
                $site->save();
                $server->runTask(new ReloadCaddy($site))->asRoot()->dispatch();
                $server->runTask(new FixDeployTask($site))->asUser()->dispatch();
                try {
                    $bing = new BingAPI('https://'.$site->address.'/api');

                    $accounts = [];
                    try {
                        $accounts = $bing->accounts()->data;
                    } catch (\Exception $e) {
                        continue;
                    }

                    if(count($accounts)){
                        foreach ($accounts as $account) {
                            $checkAccount = Account::where('email', $account->email)->first();
                            if (! $checkAccount) {
                                $todayPoints = 0;
                                if (count($account->get_points)) {
                                    if (isset($account->get_points[0]) && isset($account->get_points[1])) {
                                        $todayPoints = $account->get_points[0]->points - $account->get_points[1]->points;
                                    }
                                }
                                $newAccount = new Account();
                                $newAccount->site_id = $site->id;
                                $newAccount->account_id = $account->id;
                                $newAccount->email = $account->email;
                                $newAccount->password = $account->password;
                                $newAccount->ip = $account->ip;
                                $newAccount->web = $account->web;
                                $newAccount->mobile = $account->mobile;
                                $newAccount->links = $account->links;
                                $newAccount->timer = $account->timer;
                                $newAccount->timer_to = $account->timer_to;
                                $newAccount->done = $account->done;
                                $newAccount->run = $account->run;
                                $newAccount->fail = $account->fail;
                                $newAccount->blocked = $account->blocked;
                                $newAccount->login = $account->login;
                                $newAccount->webLogin = $account->webLogin;
                                $newAccount->points = $account->points;
                                $newAccount->created = $account->created;
                                $newAccount->gmail = $account->gmail;
                                $newAccount->suspended = $account->suspended;
                                $newAccount->points = $account->points;
                                $newAccount->today = $todayPoints;
                                $newAccount->save();
                            }
                            else {
                                $todayPoints = 0;
                                if (count($account->get_points)) {
                                    if (isset($account->get_points[0]) && isset($account->get_points[1])) {
                                        $todayPoints = $account->get_points[0]->points - $account->get_points[1]->points;
                                    }
                                }
                                $checkAccount->account_id = $account->id;
                                $checkAccount->web = $account->web;
                                $checkAccount->mobile = $account->mobile;
                                $checkAccount->links = $account->links;
                                $checkAccount->timer = $account->timer;
                                $checkAccount->timer_to = $account->timer_to;
                                $checkAccount->done = $account->done;
                                $checkAccount->run = $account->run;
                                $checkAccount->fail = $account->fail;
                                $checkAccount->blocked = $account->blocked;
                                $checkAccount->login = $account->login;
                                $checkAccount->webLogin = $account->webLogin;
                                $checkAccount->points = $account->points;
                                $checkAccount->created = $account->created;
                                $checkAccount->gmail = $account->gmail;
                                $checkAccount->suspended = $account->suspended;
                                $checkAccount->points = $account->points;
                                $checkAccount->today = $todayPoints;
                                $checkAccount->save();
                            }
                        }
                    }
                    else {
                        $siteAccounts = Account::where('site_id', $site->id)->get();
                        if(count($siteAccounts)){
                            foreach ($siteAccounts as $siteAccount) {
                                FireEventAPI::dispatch('create', 'https://'.$site->address.'/api', $siteAccount->toArray());
                            }
                        }
                    }


                    try {
                        $settings = $bing->settings()->data;
                        $site->settings = $settings;
                        $site->save();
                    }catch (\Exception $e) {
                        continue;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
        }
        else {
            $site = Site::where('server_id', $this->server->id)->firstOrFail();
            $this->server->runTask(new AptUpgrade())->asRoot()->dispatch();
            $site->tls_setting = TlsSetting::Auto;
            $site->save();
            $this->server->runTask(new ReloadCaddy($site))->asRoot()->dispatch();
            $this->server->runTask(new FixDeployTask($site))->asUser()->dispatch();
            try {
                $bing = new BingAPI('https://'.$site->address.'/api');

                $accounts = [];
                try {
                    $accounts = $bing->accounts()->data;
                } catch (\Exception $e) {}

                if(count($accounts)){
                    foreach ($accounts as $account) {
                        $checkAccount = Account::where('email', $account->email)->first();
                        if (! $checkAccount) {
                            $todayPoints = 0;
                            if (count($account->get_points)) {
                                if (isset($account->get_points[0]) && isset($account->get_points[1])) {
                                    $todayPoints = $account->get_points[0]->points - $account->get_points[1]->points;
                                }
                            }
                            $newAccount = new Account();
                            $newAccount->site_id = $site->id;
                            $newAccount->account_id = $account->id;
                            $newAccount->email = $account->email;
                            $newAccount->password = $account->password;
                            $newAccount->ip = $account->ip;
                            $newAccount->web = $account->web;
                            $newAccount->mobile = $account->mobile;
                            $newAccount->links = $account->links;
                            $newAccount->timer = $account->timer;
                            $newAccount->timer_to = $account->timer_to;
                            $newAccount->done = $account->done;
                            $newAccount->run = $account->run;
                            $newAccount->fail = $account->fail;
                            $newAccount->blocked = $account->blocked;
                            $newAccount->login = $account->login;
                            $newAccount->webLogin = $account->webLogin;
                            $newAccount->points = $account->points;
                            $newAccount->created = $account->created;
                            $newAccount->gmail = $account->gmail;
                            $newAccount->suspended = $account->suspended;
                            $newAccount->points = $account->points;
                            $newAccount->today = $todayPoints;
                            $newAccount->save();
                        }
                        else {
                            $todayPoints = 0;
                            if (count($account->get_points)) {
                                if (isset($account->get_points[0]) && isset($account->get_points[1])) {
                                    $todayPoints = $account->get_points[0]->points - $account->get_points[1]->points;
                                }
                            }
                            $checkAccount->account_id = $account->id;
                            $checkAccount->web = $account->web;
                            $checkAccount->mobile = $account->mobile;
                            $checkAccount->links = $account->links;
                            $checkAccount->timer = $account->timer;
                            $checkAccount->timer_to = $account->timer_to;
                            $checkAccount->done = $account->done;
                            $checkAccount->run = $account->run;
                            $checkAccount->fail = $account->fail;
                            $checkAccount->blocked = $account->blocked;
                            $checkAccount->login = $account->login;
                            $checkAccount->webLogin = $account->webLogin;
                            $checkAccount->points = $account->points;
                            $checkAccount->created = $account->created;
                            $checkAccount->gmail = $account->gmail;
                            $checkAccount->suspended = $account->suspended;
                            $checkAccount->points = $account->points;
                            $checkAccount->today = $todayPoints;
                            $checkAccount->save();
                        }
                    }
                }
                else {
                    $siteAccounts = Account::where('site_id', $site->id)->get();
                    if(count($siteAccounts)){
                        foreach ($siteAccounts as $siteAccount) {
                            FireEventAPI::dispatch('create', 'https://'.$site->address.'/api', $siteAccount->toArray());
                        }
                    }
                }


                try {
                    $settings = $bing->settings()->data;
                    $site->settings = $settings;
                    $site->save();
                }catch (\Exception $e) {}


            } catch (\Exception $e) {}
        }

    }
}
