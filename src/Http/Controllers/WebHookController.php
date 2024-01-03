<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Log;
use App\Models\Site;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebHookController extends Controller
{
    public function index(Request $request)
    {
        $action = $request->get('action');
        $data = $request->get('data');
        if ($request->has('action') && $request->has('data')) {
            $site = Site::where('address', 'LIKE', '%'.Str::replace('https://', '', $data['site']).'%')->first();
            if($site){
                if ($action === 'account') {
                    $account = Account::where('account_id', $data['id'])->where('site_id', $site->id)->first();
                    if ($account) {
                        $todayPoints = 0;
                        if (isset($data['get_points']) && count($data['get_points'])) {
                            if (isset($data['get_points'][0]) && isset($data['get_points'][1])) {
                                $todayPoints = $data['get_points'][0]->points - $data['get_points'][1]->points;
                            }
                        }
                        $account->web = $data['web'];
                        $account->mobile = $data['mobile'];
                        $account->links = $data['links'];
                        $account->done = $data['done'];
                        $account->run = $data['run'];
                        $account->fail = $data['fail'];
                        $account->blocked = $data['blocked'];
                        $account->login = $data['login'];
                        $account->webLogin = $data['webLogin'];
                        $account->points = $data['points'];
                        $account->created = $data['created'];
                        $account->gmail = $data['gmail'];
                        $account->suspended = $data['suspended'];
                        $account->points = $data['points'];
                        $account->today = $todayPoints;
                        $account->save();
                    }

                    return 'ok';
                }
                if ($action === 'accounts') {
                    foreach ($data['accounts'] as $account) {
                        $account = Account::where('account_id', $account['id'])->where('site_id', $site->id)->first();
                        if ($account) {
                            $todayPoints = 0;
                            if (isset($data['get_points']) && count($data['get_points'])) {
                                if (isset($data['get_points'][0]) && isset($data['get_points'][1])) {
                                    $todayPoints = $data['get_points'][0]->points - $data['get_points'][1]->points;
                                }
                            }
                            $account->web = $data['web'];
                            $account->mobile = $data['mobile'];
                            $account->links = $data['links'];
                            $account->done = $data['done'];
                            $account->run = $data['run'];
                            $account->fail = $data['fail'];
                            $account->blocked = $data['blocked'];
                            $account->login = $data['login'];
                            $account->webLogin = $data['webLogin'];
                            $account->points = $data['points'];
                            $account->created = $data['created'];
                            $account->gmail = $data['gmail'];
                            $account->suspended = $data['suspended'];
                            $account->points = $data['points'];
                            $account->today = $todayPoints;
                            $account->save();
                        }
                    }

                    return 'ok';
                }
                if ($action === 'log') {
                    Log::create([
                        'log_id' => $data['id'],
                        'account_id' => Account::where('account_id', $data['account_id'])->first()?->id,
                        'site_id' => $site->id,
                        'log' => $data['log'],
                        'type' => $data['type'],
                    ]);

                    return 'ok';
                }
                if ($action === 'logs') {
                    Log::where('site_id', $site->id)->delete();
                    foreach ($data['logs'] as $log) {
                        Log::create([
                            'log_id' => $log['id'],
                            'site_id' => $site->id,
                            'log' => $log['log'],
                            'type' => $log['type'],
                        ]);
                    }

                    return 'ok';
                }
            }
            else {
                return 'error';
            }

        }

        return 'error';

    }
}
