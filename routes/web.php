<?php

use \Illuminate\Support\Facades\Route;

Route::middleware(['web', 'auth', 'verified', 'splade'])->prefix('admin')->name('admin.')->group(function(){
    //Servers
    Route::resource('servers', \TomatoPHP\TomatoEddy\Http\Controllers\ServerController::class);
    Route::get('servers/provider/{credentials}/regions', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerProviderController::class, 'regions'])->name('servers.provider.regions');
    Route::get('servers/provider/{credentials}/types/{region}', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerProviderController::class, 'types'])->name('servers.provider.types');
    Route::get('servers/provider/{credentials}/images/{region}', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerProviderController::class, 'images'])->name('servers.provider.images');

    //Credentials
    Route::resource('credentials', \TomatoPHP\TomatoEddy\Http\Controllers\CredentialsController::class)->parameters(['credentials' => 'credentials'])->except('show');

    //SSH Keys
    Route::resource('ssh-keys', \TomatoPHP\TomatoEddy\Http\Controllers\SshKeyController::class)->only(['index', 'create', 'store', 'destroy']);
    Route::get('ssh-keys/{ssh_key}/servers/add', [\TomatoPHP\TomatoEddy\Http\Controllers\AddSshKeyToServerController::class, 'create'])->name('ssh-keys.servers.add-form');
    Route::post('ssh-keys/{ssh_key}/servers/add', [\TomatoPHP\TomatoEddy\Http\Controllers\AddSshKeyToServerController::class, 'store'])->name('ssh-keys.servers.add');
    Route::get('ssh-keys/{ssh_key}/servers/remove', [\TomatoPHP\TomatoEddy\Http\Controllers\RemoveSshKeyFromServerController::class, 'edit'])->name('ssh-keys.servers.remove-form');
    Route::post('ssh-keys/{ssh_key}/servers/remove', [\TomatoPHP\TomatoEddy\Http\Controllers\RemoveSshKeyFromServerController::class, 'destroy'])->name('ssh-keys.servers.remove');
});


Route::middleware('web', 'auth', 'verified', 'splade')->prefix('admin')->name('admin.')->group(function () {
    Route::get('github/redirect', [\TomatoPHP\TomatoEddy\Http\Controllers\GithubController::class, 'redirect'])->name('github.redirect');
    Route::get('github/callback', [\TomatoPHP\TomatoEddy\Http\Controllers\GithubController::class, 'callback'])->name('github.callback');
    Route::get('github/repositories', [\TomatoPHP\TomatoEddy\Http\Controllers\GithubController::class, 'repositories'])->name('github.repositories');
});

//
//Route::any('/deploy/{site}/{token}', [SiteDeploymentController::class, 'deployWithToken'])->name('site.deployWithToken');
//
//Route::middleware('signed:relative')->group(function () {
//    Route::get('/servers/{server}/provision-script', ServerProvisionScriptController::class)->name('servers.provisionScript');
//    Route::post('/webhook/task/{task}/timeout', [TaskWebhookController::class, 'markAsTimedOut'])->name('webhook.task.markAsTimedOut');
//    Route::post('/webhook/task/{task}/failed', [TaskWebhookController::class, 'markAsFailed'])->name('webhook.task.markAsFailed');
//    Route::post('/webhook/task/{task}/finished', [TaskWebhookController::class, 'markAsFinished'])->name('webhook.task.markAsFinished');
//    Route::post('/webhook/task/{task}/callback', [TaskWebhookController::class, 'callback'])->name('webhook.task.callback');
//});
//
$authMiddleware = [
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    VerifySubscriptionStatus::class,
];

//
//
//
//Route::middleware('splade')->group(function () use ($authMiddleware) {
//    Route::middleware($authMiddleware)->group(function () {
//
//        Route::name('servers.')->group(function () {
//            Route::get('servers/tags', [App\Http\Controllers\TagController::class, 'index'])->name('tags.index');
//            Route::get('servers/tags/api', [App\Http\Controllers\TagController::class, 'api'])->name('tags.api');
//            Route::get('servers/tags/create', [App\Http\Controllers\TagController::class, 'create'])->name('tags.create');
//            Route::post('servers/tags', [App\Http\Controllers\TagController::class, 'store'])->name('tags.store');
//            Route::get('servers/tags/{model}', [App\Http\Controllers\TagController::class, 'show'])->name('tags.show');
//            Route::get('servers/tags/{model}/edit', [App\Http\Controllers\TagController::class, 'edit'])->name('tags.edit');
//            Route::post('servers/tags/{model}', [App\Http\Controllers\TagController::class, 'update'])->name('tags.update');
//            Route::delete('servers/tags/{model}', [App\Http\Controllers\TagController::class, 'destroy'])->name('tags.destroy');
//        });
//
//        Route::view('/no-subscription', 'no-subscription')->name('no-subscription')->withoutMiddleware(VerifySubscriptionStatus::class);
//        Route::redirect('/dashboard', '/servers')->name('dashboard');
//
//        Route::get('accounts', [\App\Http\Controllers\AccountsController::class, 'all'])->name('accounts.all');
//        Route::get('site/{site}/settings', [SiteController::class, 'settings'])->name('site.settings');
//        Route::post('site/{site}/settings', [SiteController::class, 'settingsUpdate'])->name('site.settings.update');
//        Route::get('activities', [\App\Http\Controllers\ActivitiesControllers::class, 'all'])->name('activities.all');
//        Route::get('actions', [\App\Http\Controllers\ActionsControllers::class, 'index'])->name('actions.fire');
//
//        Route::resource('credentials', CredentialsController::class)->parameters(['credentials' => 'credentials'])->except('show');
//        Route::get('actions/{server}/edit', [\App\Http\Controllers\ActionsControllers::class, 'edit'])->name('servers.actions.edit');
//        Route::post('actions/{server}', [\App\Http\Controllers\ActionsControllers::class, 'update'])->name('servers.actions.update');
//        Route::get('servers/build', [ServerController::class, 'build'])->name('servers.build');
//        Route::get('servers/sync', [ServerController::class, 'sync'])->name('servers.sync');
//        Route::get('servers/scan', [ServerController::class, 'scan'])->name('servers.scan');
//        Route::get('servers/restart-all', [ServerController::class, 'restartAll'])->name('servers.restart.all');
//        Route::resource('servers', ServerController::class);
//        Route::post('servers/{server}/restart', [ServerController::class, 'restart'])->name('servers.restart');
//        Route::post('servers/{server}/stop', [ServerController::class, 'stop'])->name('servers.stop');
//        Route::post('servers/{server}/start', [ServerController::class, 'start'])->name('servers.start');
//        Route::get('servers/{server}/reset', [ServerController::class, 'resetView'])->name('servers.reset.view');
//        Route::post('servers/{server}/reset', [ServerController::class, 'reset'])->name('servers.reset');
//        Route::get('servers/{server}/storage', [ServerController::class, 'storageView'])->name('servers.storage.view');
//        Route::post('servers/{server}/storage', [ServerController::class, 'storage'])->name('servers.storage');
//        Route::resource('ssh-keys', SshKeyController::class)->only(['index', 'create', 'store', 'destroy']);
//
//        Route::middleware('can:manage,ssh_key')->group(function () {
//            Route::get('ssh-keys/{ssh_key}/servers/add', [AddSshKeyToServerController::class, 'create'])->name('ssh-keys.servers.add-form');
//            Route::post('ssh-keys/{ssh_key}/servers/add', [AddSshKeyToServerController::class, 'store'])->name('ssh-keys.servers.add');
//            Route::get('ssh-keys/{ssh_key}/servers/remove', [RemoveSshKeyFromServerController::class, 'edit'])->name('ssh-keys.servers.remove-form');
//            Route::post('ssh-keys/{ssh_key}/servers/remove', [RemoveSshKeyFromServerController::class, 'destroy'])->name('ssh-keys.servers.remove');
//        });
//
//        Route::middleware('can:view,credentials')->group(function () {
//            Route::get('servers/provider/{credentials}/regions', [ServerProviderController::class, 'regions'])->name('servers.provider.regions');
//            Route::get('servers/provider/{credentials}/types/{region}', [ServerProviderController::class, 'types'])->name('servers.provider.types');
//            Route::get('servers/provider/{credentials}/images/{region}', [ServerProviderController::class, 'images'])->name('servers.provider.images');
//        });
//
//        Route::middleware('can:manage,server')->group(function () {
//            Route::resource('servers.crons', CronController::class);
//            Route::resource('servers.daemons', DaemonController::class);
//            Route::resource('servers.databases', DatabaseController::class)->except(['show', 'update']);
//            Route::resource('servers.database-users', DatabaseUserController::class)->except(['index', 'show']);
//            Route::resource('servers.files', FileController::class)->only(['index', 'show', 'edit', 'update']);
//            Route::resource('servers.firewall-rules', FirewallRuleController::class);
//            Route::resource('servers.sites', SiteController::class);
//            Route::get('servers/{server}/logs', LogController::class)->name('servers.logs.index');
//            Route::get('servers/{server}/software', [SoftwareController::class, 'index'])->name('servers.software.index');
//            Route::post('servers/{server}/software/{software}/default', [SoftwareController::class, 'default'])->name('servers.software.default');
//            Route::post('servers/{server}/software/{software}/restart', [SoftwareController::class, 'restart'])->name('servers.software.restart');
//
//            Route::middleware('can:manage,site')
//                ->name('servers.sites.')
//                ->prefix('servers/{server}/sites/{site}')
//                ->group(function () {
//                    Route::get('deployment-settings', [SiteDeploymentSettingsController::class, 'edit'])->name('deployment-settings.edit');
//                    Route::patch('deployment-settings', [SiteDeploymentSettingsController::class, 'update'])->name('deployment-settings.update');
//                    Route::post('deploy-token', SiteDeployTokenController::class)->name('refresh-deploy-token');
//                    Route::resource('deployments', SiteDeploymentController::class)->only(['index', 'show', 'store']);
//                    Route::get('files', [SiteFileController::class, 'index'])->name('files.index');
//                    Route::get('ssl', [SiteSslController::class, 'edit'])->name('ssl.edit');
//                    Route::patch('ssl', [SiteSslController::class, 'update'])->name('ssl.update');
//                    Route::get('logs', [SiteLogController::class, 'index'])->name('logs.index');
//                });
//        });
//    });
//});
