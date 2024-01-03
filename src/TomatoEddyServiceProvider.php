<?php

namespace TomatoPHP\TomatoEddy;

use Illuminate\Support\ServiceProvider;
use TomatoPHP\TomatoAdmin\Facade\TomatoMenu;
use TomatoPHP\TomatoAdmin\Services\Contracts\Menu;


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
                ->label(__('SSH Keys'))
                ->icon('bx bxs-key')
                ->route('admin.ssh-keys.index'),
           Menu::make()
            ->group(__('Eddy'))
            ->label(__('Servers'))
            ->icon('bx bxs-group')
            ->route('admin.servers.index')
        ]);
    }
}
