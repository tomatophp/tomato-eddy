<?php

namespace App\Jobs;

use App\Models\Account;
use App\Models\Log;
use App\Services\BingAPI;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class FireEventAPI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public string $type,
        public string $endpoint,
        public array|int|null $data = null
    ) {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $bing = new BingAPI($this->endpoint);

        if ($this->type === 'check') {
            $bing->check($this->data);
        }

        if ($this->type === 'create') {
            $response = $bing->store($this->data);
            $account = Account::find($this->data['id']);
            $account->account_id = $response->data->id;
            $account->save();
        }

        if ($this->type === 'open') {
            $bing->open($this->data);
        }

        if ($this->type === 'destroy') {
            $bing->destory($this->data['account_id']);
        }

        if ($this->type === 'hold-account') {
            $bing->bulkHold($this->data);
        }

        if ($this->type === 'unhold-account') {
            $bing->bulkHold($this->data);
        }

        if ($this->type === 'hold') {

            $bing->hold();
        }

        if ($this->type === 'unhold') {
            $bing->unhold();
        }

        if ($this->type === 'scan') {
            if($this->data['account_id']){
                $account = $bing->get($this->data['account_id'])->data;
                $checkAccount = Account::where('email', $account->email)->first();
                if (! $checkAccount) {
                    $todayPoints = 0;
                    if (isset($account->get_points) && count($account->get_points)) {
                        if (isset($account->get_points[0]) && isset($account->get_points[1])) {
                            $todayPoints = $account->get_points[0]->points - $account->get_points[1]->points;
                        }
                    }
                    $newAccount = new Account();
                    $newAccount->site_id = $this->data['site_id'];
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
                } else {
                    $todayPoints = 0;
                    if (isset($account->get_points) && count($account->get_points)) {
                        if (isset($account->get_points[0]) && isset($account->get_points[1])) {
                            $todayPoints = $account->get_points[0]->points - $account->get_points[1]->points;
                        }
                    }
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
            else {
                try{
                    $response = $bing->store($this->data);
                    $account = Account::find($this->data['id']);
                    $account->account_id = $response->data->id;
                    $account->save();
                }catch (\Exception $exception) {
                }
            }

        }

        if($this->type === 'settings'){
            $bing->updateSettings($this->data);
        }

        if ($this->type === 'logs') {
            $logs = $bing->logs();
            foreach ($logs->data as $log) {
                $createLog = new Log();
                $createLog->log_id = $log->id;
                $createLog->site_id = $this->data['site_id'];
                $createLog->account_id = Account::where('account_id', $log->account_id)->first()->id;
                $createLog->log = $log->log;
                $createLog->type = $log->type;
                $createLog->created_at = $log->created_at;
                $createLog->save();
            }
        }

        if ($this->type === 'cookies') {
            $bing->cookies($this->data);
        }

        if ($this->type === 'run') {
            $bing->run($this->data['account_id']);
        }

        if ($this->type === 'stop') {
            $bing->stop();
        }

        if ($this->type === 'checkBulk') {
            $bing->checkBulk($this->data);
        }

        if ($this->type === 'cookiesBulk') {
            $bing->cookiesBulk($this->data);
        }

        if ($this->type === 'runBulk') {
            $bing->runBulk($this->data);
        }

        if ($this->type === 'checkAll') {
            $bing->checkAll();
        }

        if ($this->type === 'checkAllWeb') {
            $bing->checkAll('web');
        }

        if ($this->type === 'checkAllMobile') {
            $bing->checkAll('mobile');
        }

        if ($this->type === 'cookiesAll') {
            $bing->cookiesAll();
        }

        if ($this->type === 'runAll') {
            $bing->runAll();
        }

        if ($this->type === 'runAllSearch') {
            $bing->runAllSearch();
        }

        if ($this->type === 'runAllLinks') {
            $bing->runAllLinks();
        }

        if ($this->type === 'clear-mobile') {
            $bing->clearMobile();
        }

        if ($this->type === 'clear-suspended') {
            $bing->clearSuspended();
        }
    }
}
