<?php

namespace MarkWalet\DotenvManager;

use Illuminate\Support\ServiceProvider;
use MarkWalet\DotenvManager\Adapters\FileDotenvAdapter;
use MarkWalet\DotenvManager\Commands\AddDotenvValueCommand;
use MarkWalet\DotenvManager\Commands\RemoveDotenvValueCommand;
use MarkWalet\DotenvManager\Commands\SetDotenvValueCommand;

class DotenvManagerServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();
        $this->registerCommands();
    }

    /**
     * Register the manager instance.
     *
     * @return void
     */
    private function registerManager()
    {
        $this->app->singleton(DotenvManager::class, function () {
            return new DotenvManager(
                new FileDotenvAdapter(base_path('.env'))
            );
        });
    }

    /**
     * Register the artisan commands.
     *
     * @return void
     */
    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AddDotenvValueCommand::class,
                SetDotenvValueCommand::class,
                RemoveDotenvValueCommand::class,
            ]);
        }
    }
}