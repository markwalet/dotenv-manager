<?php

namespace MarkWalet\DotenvManager\Tests;

use MarkWalet\DotenvManager\DotenvManager;

class DotenvManagerServiceProviderTest extends LaravelTestCase
{
    /** @test */
    public function it_binds_manager_to_the_application()
    {
        $bindings = $this->app->getBindings();

        $this->assertArrayHasKey(DotenvManager::class, $bindings);
    }
}
