<?php

namespace MarkWalet\DotenvManager\Tests;

use Illuminate\Foundation\Application;
use MarkWalet\DotenvManager\DotenvManagerServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class LaravelTestCase extends BaseTestCase
{
    /**
     * Get package providers.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [DotenvManagerServiceProvider::class];
    }
}
