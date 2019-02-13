<?php

use MarkWalet\DotenvManager\Adapters\FileDotenvAdapter;
use PHPUnit\Framework\TestCase;

class FileDotenvAdapterTest extends TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        file_put_contents(__DIR__.'/.env.testing', 'Sample content');
        file_put_contents(__DIR__.'/.env.other', 'Other content');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unlink(__DIR__.'/.env.testing');
        unlink(__DIR__.'/.env.other');
    }

    /** @test */
    public function can_read_file()
    {
        $adapter = new FileDotenvAdapter(__DIR__.'/.env.testing');

        $content = $adapter->read();

        $this->assertEquals("Sample content", $content);
    }

    /** @test */
    public function can_write_file()
    {
        $adapter = new FileDotenvAdapter(__DIR__.'/.env.testing');

        $adapter->write("Updated content");
        $content = $adapter->read();

        $this->assertEquals('Updated content', $content);
    }

    /** @test */
    public function two_instances_can_read_two_different_paths_at_the_same_time()
    {
        $adapterA = new FileDotenvAdapter(__DIR__.'/.env.testing');
        $adapterB = new FileDotenvAdapter(__DIR__.'/.env.other');

        $contentA = $adapterA->read();
        $contentB = $adapterB->read();

        $this->assertEquals('Sample content', $contentA);
        $this->assertEquals('Other content', $contentB);
    }

    /** @test */
    public function two_instances_can_read_the_same_path_at_the_same_time()
    {
        $adapterA = new FileDotenvAdapter(__DIR__.'/.env.testing');
        $adapterB = new FileDotenvAdapter(__DIR__.'/.env.testing');

        $contentA = $adapterA->read();
        $contentB = $adapterB->read();

        $this->assertEquals('Sample content', $contentA);
        $this->assertEquals('Sample content', $contentB);
    }
}
