<?php

namespace TomatoPHP\TomatoEddy;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;
use TomatoPHP\TomatoAdmin\Facade\TomatoMenu;
use TomatoPHP\TomatoAdmin\Services\Contracts\Menu;
use TomatoPHP\TomatoEddy\Http\Resources\CredentialsResource;
use TomatoPHP\TomatoEddy\Http\Resources\CronResource;
use TomatoPHP\TomatoEddy\Http\Resources\DaemonResource;
use TomatoPHP\TomatoEddy\Http\Resources\DatabaseResource;
use TomatoPHP\TomatoEddy\Http\Resources\DatabaseUserResource;
use TomatoPHP\TomatoEddy\Http\Resources\FirewallRuleResource;
use TomatoPHP\TomatoEddy\Http\Resources\ServerResource;
use TomatoPHP\TomatoEddy\Http\Resources\SiteResource;
use TomatoPHP\TomatoEddy\Http\Resources\TeamResource;
use TomatoPHP\TomatoEddy\Http\Resources\UserResource;
use TomatoPHP\TomatoEddy\Models\Credentials;
use TomatoPHP\TomatoEddy\Models\Cron;
use TomatoPHP\TomatoEddy\Models\Daemon;
use TomatoPHP\TomatoEddy\Models\Database;
use TomatoPHP\TomatoEddy\Models\DatabaseUser;
use TomatoPHP\TomatoEddy\Models\Deployment;
use TomatoPHP\TomatoEddy\Models\FirewallRule;
use TomatoPHP\TomatoEddy\Models\Server;
use TomatoPHP\TomatoEddy\Models\Site;
use TomatoPHP\TomatoEddy\Models\Team;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel as ConsoleKernel;
use Illuminate\Contracts\Http\Kernel as HttpKernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\GithubProvider;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use ProtoneMedia\Splade\Components\Form\Select;
use ProtoneMedia\Splade\Facades\Splade;
use ProtoneMedia\Splade\SpladeTable;
use ProtoneMedia\Splade\SpladeToast;
use TomatoPHP\TomatoEddy\View\Components\NavigationItem;
use TomatoPHP\TomatoEddy\View\Components\PrismEditor;
use TomatoPHP\TomatoEddy\View\Components\PrismViewer;
use TomatoPHP\TomatoEddy\View\Components\ServerLayout;
use TomatoPHP\TomatoEddy\View\Components\SiteCaddyfile;
use TomatoPHP\TomatoEddy\View\Components\SiteLayout;
use TomatoPHP\TomatoEddy\View\Components\SupervisorProgram;
use TomatoPHP\TomatoEddy\View\Components\TaskCallback;
use TomatoPHP\TomatoEddy\View\Components\TaskShellDefaults;


class TomatoEddyServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //Register generate command
        $this->commands([
           \TomatoPHP\TomatoEddy\Console\TomatoEddyInstall::class,
        ]);

        //Register Config file
        $this->mergeConfigFrom(__DIR__.'/../config/tomato-eddy.php', 'tomato-eddy');

        //Publish Config
        $this->publishes([
           __DIR__.'/../config/tomato-eddy.php' => config_path('tomato-eddy.php'),
        ], 'tomato-eddy-config');

        //Register Migrations
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        //Publish Migrations
        $this->publishes([
           __DIR__.'/../database/migrations' => database_path('migrations'),
        ], 'tomato-eddy-migrations');
        //Register views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'tomato-eddy');

        //Publish Views
        $this->publishes([
           __DIR__.'/../resources/views' => resource_path('views/vendor/tomato-eddy'),
        ], 'tomato-eddy-views');

        //Register Langs
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'tomato-eddy');

        //Publish Lang
        $this->publishes([
           __DIR__.'/../resources/lang' => base_path('lang/vendor/tomato-eddy'),
        ], 'tomato-eddy-lang');

        //Register Routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

        Config::set('task-runner.task_views', 'tomato-eddy::tasks');

        $this->loadViewComponentsAs('eddy', [
            Cron::class,
            NavigationItem::class,
            PrismEditor::class,
            PrismViewer::class,
            ServerLayout::class,
            SiteCaddyfile::class,
            SiteLayout::class,
            SupervisorProgram::class,
            TaskCallback::class,
            TaskShellDefaults::class
        ]);

    }

    public function boot(): void
    {
        TomatoMenu::register([
            Menu::make()
                ->group(__('Eddy'))
                ->label(__('Credentials'))
                ->icon('bx bxs-lock')
                ->route('admin.credentials.index'),
            Menu::make()
                ->group(__('Eddy'))
                ->label(__('Recipes'))
                ->icon('bx bxs-receipt')
                ->route('admin.recipes.index'),
            Menu::make()
                ->group(__('Eddy'))
                ->label(__('SSH Keys'))
                ->icon('bx bxs-key')
                ->route('admin.ssh-keys.index'),
            Menu::make()
                ->group(__('Eddy'))
                ->label(__('Sites Templates'))
                ->icon('bx bx-globe')
                ->route('admin.site-templates.index'),
           Menu::make()
            ->group(__('Eddy'))
            ->label(__('Servers'))
            ->icon('bx bxs-group')
            ->route('admin.servers.index')
        ]);


        Str::macro('generateWordpressKey', function ($length = 64) {
            $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()';
            $max = strlen($chars) - 1;

            $key = '';

            for ($i = 0; $i < $length; $i++) {
                $key .= substr($chars, random_int(0, $max), 1);
            }

            return $key;
        });

    }


    private function enableSafetyMechanisms()
    {
        if ($this->app->runningInConsole()) {
            // Log slow commands.
            $this->app[ConsoleKernel::class]->whenCommandLifecycleIsLongerThan(
                5000,
                function ($startedAt, $input, $status) {
                    // TODO: Add info about the command
                    Log::warning('A command took longer than 5 seconds.');
                }
            );
        } else {
            // Log slow requests.
            $this->app[HttpKernel::class]->whenRequestLifecycleIsLongerThan(
                5000,
                function ($startedAt, $request, $response) {
                    // TODO: Add info about the request
                    Log::warning('A request took longer than 5 seconds.');
                }
            );
        }

        // Everything strict, all the time.
        Model::shouldBeStrict();

        // But in production, log the violation instead of throwing an exception.
        if ($this->app->isProduction()) {
            Model::handleLazyLoadingViolationUsing(function ($model, $relation) {
                $class = get_class($model);

                Log::info("Attempted to lazy load [{$relation}] on model [{$class}].");
            });
        }

        // Enforce a morph map instead of making it optional.
        Relation::enforceMorphMap([
            'cron' => Cron::class,
            'daemon' => Daemon::class,
            'database_user' => DatabaseUser::class,
            'database' => Database::class,
            'deployment' => Deployment::class,
            'firewall_rule' => FirewallRule::class,
            'server' => Server::class,
            'site' => Site::class,
            'team' => Team::class,
            'user' => User::class,
        ]);

        DB::listen(function ($query) {
            if ($query->time > 1000) {
                Log::warning('An individual database query exceeded 1 second.', [
                    'sql' => $query->sql,
                ]);
            }
        });
    }
}
