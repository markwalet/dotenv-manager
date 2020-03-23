<?php

namespace MarkWalet\DotenvManager;

use Illuminate\Support\ServiceProvider;
use MarkWalet\DotenvManager\Adapters\FileDotenvAdapter;

class DotenvManagerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function register()
    {
        $this->registerManager();
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
}
