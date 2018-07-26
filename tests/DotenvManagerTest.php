<?php

namespace MarkWalet\DotenvManager\Tests;

use MarkWalet\DotenvManager\Adapters\FakeDotenvAdapter;
use MarkWalet\DotenvManager\Changes\Change;
use MarkWalet\DotenvManager\Changes\Concerns\HasKey;
use MarkWalet\DotenvManager\DotenvManager;
use MarkWalet\DotenvManager\DotenvBuilder;
use PHPUnit\Framework\TestCase;

class DotenvManagerTest extends TestCase
{
    /**
     * @var FakeDotenvAdapter
     */
    private $adapter;

    /**
     * @var DotenvManager
     */
    private $environment;

    protected function setUp()
    {
        parent::setUp();

        $this->adapter = new FakeDotenvAdapter;
        $this->environment = new DotenvManager($this->adapter);
    }

    /** @test */
    public function can_add_an_environment_line()
    {
        $this->adapter->setSource("TEST1=value");

        $this->environment->add('TEST2', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value".PHP_EOL."TEST2=value2", $content);
    }

    /** @test */
    public function can_create_an_environment_line()
    {
        $this->adapter->setSource("TEST1=value");

        $this->environment->create('TEST2', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value".PHP_EOL."TEST2=value2", $content);
    }

    /** @test */
    public function can_set_an_environment_line()
    {
        $this->adapter->setSource("TEST1=value");

        $this->environment->set('TEST1', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value2", $content);
    }

    /** @test */
    public function can_update_an_environment_line()
    {
        $this->adapter->setSource("TEST1=value");

        $this->environment->update('TEST1', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value2", $content);
    }

    /** @test */
    public function can_delete_an_environment_line()
    {
        $this->adapter->setSource("TEST1=value".PHP_EOL."TEST2=value2");

        $this->environment->delete('TEST1');
        $content = $this->adapter->read();

        $this->assertEquals("TEST2=value2", $content);
    }

    /** @test */
    public function can_unset_an_environment_line()
    {
        $this->adapter->setSource("TEST1=value".PHP_EOL."TEST2=value2");

        $this->environment->unset('TEST1');
        $content = $this->adapter->read();

        $this->assertEquals("TEST2=value2", $content);
    }

    /** @test */
    public function can_mutate_multiple_lines_in_an_environment_line_at_once()
    {
        $this->adapter->setSource("TEST1=value1" . PHP_EOL . "TEST2=value2" . PHP_EOL . "TEST3=value3");

        $this->environment->mutate(function (DotenvBuilder $builder) {
            $builder->add('TEST4', 'escaped value');
            $builder->update('TEST2', 'updated')->after('TEST3');
            $builder->delete('TEST1');
            $builder->move('TEST4')->before('TEST3');
        });
        $content = $this->adapter->read();

        $this->assertEquals("TEST4=\"escaped value\"".PHP_EOL."TEST3=value3".PHP_EOL."TEST2=updated", $content);
    }

    /** @test */
    public function can_extend_builder()
    {
        $this->adapter->setSource("INTEGER_VALUE=111");
        $this->environment->extend('increment', Increment::class);

        $this->environment->increment('INTEGER_VALUE');
        $content = $this->adapter->read();

        $this->assertEquals("INTEGER_VALUE=112", $content);
    }
}

class Increment extends Change
{
    use HasKey;

    function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Apply the pending change to the given content.
     *
     * @param $content
     *
     * @return mixed
     */
    public function apply(string $content): string
    {
        $search = '/'.$this->getKey().'=(.*)/';
        preg_match($search, $content, $matches);
        $value = $matches[1];

        $replacement = $this->getKey().'='.($value + 1);

        return preg_replace($search, $replacement, $content);
    }
}