<?php

use MarkWalet\DotenvManager\Adapters\FileDotenvAdapter;
use PHPUnit\Framework\TestCase;

class FileDotenvAdapterTest extends TestCase
{
    /**
     * @var string
     */
    private $path;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->path = __DIR__ . '/.env.testing';
        $content = "TEST1=value1" . PHP_EOL . "TEST2=value2" . PHP_EOL . "TEST3=value3";

        file_put_contents($this->path, $content);
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
        unlink($this->path);
    }

    /** @test */
    public function can_read_file()
    {
        $adapter = new FileDotenvAdapter($this->path);

        $content = $adapter->read();

        $this->assertEquals("TEST1=value1" . PHP_EOL . "TEST2=value2" . PHP_EOL . "TEST3=value3", $content);
    }

    /** @test */
    public function can_write_file()
    {
        $adapter = new FileDotenvAdapter($this->path);

        $adapter->write("TEST1=updated" . PHP_EOL . "TEST2=updated");
        $content = $adapter->read();

        $this->assertEquals("TEST1=updated" . PHP_EOL . "TEST2=updated", $content);
    }
}