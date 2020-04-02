<?php

namespace MarkWalet\DotenvManager;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use MarkWalet\DotenvManager\Adapters\FileDotenvAdapter;

class DotenvManagerServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(DotenvManager::class, function (Application $app) {
            return new DotenvManager(
                new FileDotenvAdapter($app->basePath('.env'))
            );
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [DotenvManager::class];
    }
}
