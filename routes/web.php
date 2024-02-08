<?php

use \Illuminate\Support\Facades\Route;

Route::any('/deploy/{site}/{token}', [\TomatoPHP\TomatoEddy\Http\Controllers\SiteDeploymentController::class, 'deployWithToken'])->name('site.deployWithToken');

Route::middleware(['web', 'signed:relative'])->group(function () {
    Route::get('/servers/{server}/provision-script', \TomatoPHP\TomatoEddy\Http\Controllers\ServerProvisionScriptController::class)->name('servers.provisionScript');
    Route::post('/webhook/task/{task}/timeout', [\TomatoPHP\TomatoEddy\Http\Controllers\TaskWebhookController::class, 'markAsTimedOut'])->name('webhook.task.markAsTimedOut');
    Route::post('/webhook/task/{task}/failed', [\TomatoPHP\TomatoEddy\Http\Controllers\TaskWebhookController::class, 'markAsFailed'])->name('webhook.task.markAsFailed');
    Route::post('/webhook/task/{task}/finished', [\TomatoPHP\TomatoEddy\Http\Controllers\TaskWebhookController::class, 'markAsFinished'])->name('webhook.task.markAsFinished');
    Route::post('/webhook/task/{task}/callback', [\TomatoPHP\TomatoEddy\Http\Controllers\TaskWebhookController::class, 'callback'])->name('webhook.task.callback');
});

Route::middleware(['web','auth', 'splade', 'verified'])->name('admin.')->group(function () {
    Route::get('admin/recipes', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'index'])->name('recipes.index');
    Route::get('admin/recipes/api', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'api'])->name('recipes.api');
    Route::get('admin/recipes/create', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'create'])->name('recipes.create');
    Route::post('admin/recipes', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'store'])->name('recipes.store');
    Route::get('admin/recipes/{model}', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'show'])->name('recipes.show');
    Route::get('admin/recipes/{model}/fire', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'server'])->name('recipes.server');
    Route::post('admin/recipes/{model}/fire', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'fire'])->name('recipes.fire');
    Route::get('admin/recipes/{model}/edit', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'edit'])->name('recipes.edit');
    Route::post('admin/recipes/{model}', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('admin/recipes/{model}', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'destroy'])->name('recipes.destroy');
    Route::get('admin/recipes/log/{model}', [\TomatoPHP\TomatoEddy\Http\Controllers\RecipeController::class, 'log'])->name('recipes.log.show');
});


Route::middleware(['web','auth', 'splade', 'verified'])->name('admin.')->group(function () {
    Route::get('admin/site-templates', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'index'])->name('site-templates.index');
    Route::get('admin/site-templates/api', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'api'])->name('site-templates.api');
    Route::get('admin/site-templates/create', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'create'])->name('site-templates.create');
    Route::post('admin/site-templates', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'store'])->name('site-templates.store');
    Route::get('admin/site-templates/{model}', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'show'])->name('site-templates.show');
    Route::get('admin/site-templates/{model}/edit', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'edit'])->name('site-templates.edit');
    Route::post('admin/site-templates/{model}/server', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'server'])->name('site-templates.server');
    Route::post('admin/site-templates/{model}', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'update'])->name('site-templates.update');
    Route::delete('admin/site-templates/{model}', [TomatoPHP\TomatoEddy\Http\Controllers\SiteTemplateController::class, 'destroy'])->name('site-templates.destroy');
});


Route::middleware(['web', 'auth', 'verified', 'splade'])->prefix('admin')->name('admin.')->group(function(){

    //Server Actions
    Route::get('servers-actions/build', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'build'])->name('servers.build');
    Route::get('servers-actions/sync', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'sync'])->name('servers.sync');
    Route::get('servers-actions/scan', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'scan'])->name('servers.scan');
    Route::get('servers-actions/restart-all', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'restartAll'])->name('servers.restart.all');
    Route::post('servers-actions/{server}/restart', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'restart'])->name('servers.restart');
    Route::post('servers-actions/{server}/disconect', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'disconect'])->name('servers.disconect');
    Route::post('servers-actions/{server}/stop', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'stop'])->name('servers.stop');
    Route::post('servers-actions/{server}/start', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'start'])->name('servers.start');
    Route::get('servers-actions/{server}/reset', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'resetView'])->name('servers.reset.view');
    Route::post('servers-actions/{server}/reset', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'reset'])->name('servers.reset');
    Route::get('servers-actions/{server}/storage', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'storageView'])->name('servers.storage.view');
    Route::post('servers-actions/{server}/storage', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'storage'])->name('servers.storage');
    Route::delete('servers-actions/{server}/destroy-volumes', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'destroyVolumes'])->name('servers.storage.destory');
    Route::get('servers-actions/connect', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'connect'])->name('servers.connect');
    Route::post('servers-actions/connect', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerActionsController::class, 'link'])->name('servers.link');


    //Servers
    Route::resource('servers', \TomatoPHP\TomatoEddy\Http\Controllers\ServerController::class);
    Route::resource('servers.crons', \TomatoPHP\TomatoEddy\Http\Controllers\CronController::class);
    Route::resource('servers.daemons', \TomatoPHP\TomatoEddy\Http\Controllers\DaemonController::class);
    Route::resource('servers.databases', \TomatoPHP\TomatoEddy\Http\Controllers\DatabaseController::class)->except(['show']);
    Route::resource('servers.database-users', \TomatoPHP\TomatoEddy\Http\Controllers\DatabaseUserController::class)->except(['index', 'show']);
    Route::resource('servers.files', \TomatoPHP\TomatoEddy\Http\Controllers\FileController::class)->only(['index', 'show', 'edit', 'update']);
    Route::resource('servers.firewall-rules', \TomatoPHP\TomatoEddy\Http\Controllers\FirewallRuleController::class);
    Route::resource('servers.sites', \TomatoPHP\TomatoEddy\Http\Controllers\SiteController::class);
    Route::get('servers/{server}/logs', \TomatoPHP\TomatoEddy\Http\Controllers\LogController::class)->name('servers.logs.index');
    Route::get('servers/{server}/software', [\TomatoPHP\TomatoEddy\Http\Controllers\SoftwareController::class, 'index'])->name('servers.software.index');
    Route::post('servers/{server}/software/{software}/default', [\TomatoPHP\TomatoEddy\Http\Controllers\SoftwareController::class, 'default'])->name('servers.software.default');
    Route::post('servers/{server}/software/{software}/restart', [\TomatoPHP\TomatoEddy\Http\Controllers\SoftwareController::class, 'restart'])->name('servers.software.restart');
    Route::get('servers/provider/{credentials}/regions', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerProviderController::class, 'regions'])->name('servers.provider.regions');
    Route::get('servers/provider/{credentials}/types/{region}', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerProviderController::class, 'types'])->name('servers.provider.types');
    Route::get('servers/provider/{credentials}/images/{region}', [\TomatoPHP\TomatoEddy\Http\Controllers\ServerProviderController::class, 'images'])->name('servers.provider.images');


    Route::name('servers.sites.')
        ->prefix('servers/{server}/sites/{site}')
        ->group(function () {
            Route::get('deployment-settings', [\TomatoPHP\TomatoEddy\Http\Controllers\SiteDeploymentSettingsController::class, 'edit'])->name('deployment-settings.edit');
            Route::patch('deployment-settings', [\TomatoPHP\TomatoEddy\Http\Controllers\SiteDeploymentSettingsController::class, 'update'])->name('deployment-settings.update');
            Route::post('deploy-token', \TomatoPHP\TomatoEddy\Http\Controllers\SiteDeployTokenController::class)->name('refresh-deploy-token');
            Route::resource('deployments', \TomatoPHP\TomatoEddy\Http\Controllers\SiteDeploymentController::class)->only(['index', 'show', 'store']);
            Route::get('files', [\TomatoPHP\TomatoEddy\Http\Controllers\SiteFileController::class, 'index'])->name('files.index');
            Route::get('ssl', [\TomatoPHP\TomatoEddy\Http\Controllers\SiteSslController::class, 'edit'])->name('ssl.edit');
            Route::patch('ssl', [\TomatoPHP\TomatoEddy\Http\Controllers\SiteSslController::class, 'update'])->name('ssl.update');
            Route::get('logs', [\TomatoPHP\TomatoEddy\Http\Controllers\SiteLogController::class, 'index'])->name('logs.index');
        });
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

$authMiddleware = [
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
    VerifySubscriptionStatus::class,
];

