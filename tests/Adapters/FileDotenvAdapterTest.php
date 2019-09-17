<?php

use MarkWalet\DotenvManager\Adapters\FileDotenvAdapter;
use PHPUnit\Framework\TestCase;

class FileDotenvAdapterTest extends TestCase
{

    /** @test */
    public function can_read_file()
    {
        file_put_contents(__DIR__.'/.env.testing', 'Sample content');
        file_put_contents(__DIR__.'/.env.other', 'Other content');
        $adapter = new FileDotenvAdapter(__DIR__.'/.env.testing');

        $content = $adapter->read();

        $this->assertEquals("Sample content", $content);
        unlink(__DIR__.'/.env.testing');
        unlink(__DIR__.'/.env.other');
    }

    /** @test */
    public function can_write_file()
    {
        file_put_contents(__DIR__.'/.env.testing', 'Sample content');
        file_put_contents(__DIR__.'/.env.other', 'Other content');
        $adapter = new FileDotenvAdapter(__DIR__.'/.env.testing');

        $adapter->write("Updated content");
        $content = $adapter->read();

        $this->assertEquals('Updated content', $content);
        unlink(__DIR__.'/.env.testing');
        unlink(__DIR__.'/.env.other');
    }

    /** @test */
    public function two_instances_can_read_two_different_paths_at_the_same_time()
    {
        file_put_contents(__DIR__.'/.env.testing', 'Sample content');
        file_put_contents(__DIR__.'/.env.other', 'Other content');
        $adapterA = new FileDotenvAdapter(__DIR__.'/.env.testing');
        $adapterB = new FileDotenvAdapter(__DIR__.'/.env.other');

        $contentA = $adapterA->read();
        $contentB = $adapterB->read();

        $this->assertEquals('Sample content', $contentA);
        $this->assertEquals('Other content', $contentB);
        unlink(__DIR__.'/.env.testing');
        unlink(__DIR__.'/.env.other');
    }

    /** @test */
    public function two_instances_can_read_the_same_path_at_the_same_time()
    {
        file_put_contents(__DIR__.'/.env.testing', 'Sample content');
        file_put_contents(__DIR__.'/.env.other', 'Other content');
        $adapterA = new FileDotenvAdapter(__DIR__.'/.env.testing');
        $adapterB = new FileDotenvAdapter(__DIR__.'/.env.testing');

        $contentA = $adapterA->read();
        $contentB = $adapterB->read();

        $this->assertEquals('Sample content', $contentA);
        $this->assertEquals('Sample content', $contentB);
        unlink(__DIR__.'/.env.testing');
        unlink(__DIR__.'/.env.other');
    }
}
