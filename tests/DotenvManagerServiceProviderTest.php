<?php

namespace MarkWalet\DotenvManager\Tests;

use MarkWalet\DotenvManager\DotenvManager;

class DotenvManagerServiceProviderTest extends LaravelTestCase
{
    /** @test */
    public function it_binds_a_manager_to_the_application()
    {
        $this->app->registerConfiguredProviders();
        $bindings = $this->app->getBindings();
        $this->assertArrayHasKey(DotenvManager::class, $bindings);
        
        $result = $this->app->make(DotenvManager::class);
        $this->assertInstanceOf(DotenvManager::class, $result);
    }
}
