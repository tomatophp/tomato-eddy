<?php

namespace App\Http\Controllers;

use App\Models\Server;
use App\Models\Site;

class ReportsControllers extends Controller
{
    public function index(Server $server, Site $site)
    {
        return view('sites.reports.index', compact('server', 'site'));
    }
}
