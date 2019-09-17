<?php

namespace MarkWalet\DotenvManager\Tests;

use MarkWalet\DotenvManager\Adapters\FakeDotenvAdapter;
use MarkWalet\DotenvManager\Changes\Change;
use MarkWalet\DotenvManager\Changes\Concerns\HasKey;
use MarkWalet\DotenvManager\DotenvBuilder;
use MarkWalet\DotenvManager\DotenvManager;
use MarkWalet\DotenvManager\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DotenvManagerTest extends TestCase
{
    /** @test */
    public function can_add_a_line_to_dotenv()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('TEST1=value');

        $dotenv->add('TEST2', 'value2');
        $content = $adapter->read();

        $this->assertEquals('TEST1=value'.PHP_EOL.'TEST2=value2', $content);
    }

    /** @test */
    public function can_create_a_line_in_dotenv()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('TEST1=value');

        $dotenv->create('TEST2', 'value2');
        $content = $adapter->read();

        $this->assertEquals('TEST1=value'.PHP_EOL.'TEST2=value2', $content);
    }

    /** @test */
    public function can_set_a_line_in_dotenv()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('TEST1=value');

        $dotenv->set('TEST1', 'value2');
        $content = $adapter->read();

        $this->assertEquals('TEST1=value2', $content);
    }

    /** @test */
    public function can_update_a_line_in_dotenv()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('TEST1=value');

        $dotenv->update('TEST1', 'value2');
        $content = $adapter->read();

        $this->assertEquals('TEST1=value2', $content);
    }

    /** @test */
    public function can_delete_a_line_in_dotenv()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('TEST1=value'.PHP_EOL.'TEST2=value2');

        $dotenv->delete('TEST1');
        $content = $adapter->read();

        $this->assertEquals('TEST2=value2', $content);
    }

    /** @test */
    public function can_unset_a_line_in_dotenv()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('TEST1=value'.PHP_EOL.'TEST2=value2');

        $dotenv->unset('TEST1');
        $content = $adapter->read();

        $this->assertEquals('TEST2=value2', $content);
    }

    /** @test */
    public function can_mutate_multiple_lines_at_once_in_dotenv()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('TEST1=value1'.PHP_EOL.'TEST2=value2'.PHP_EOL.'TEST3=value3');

        $dotenv->mutate(function (DotenvBuilder $builder) {
            $builder->add('TEST4', 'escaped value');
            $builder->update('TEST2', 'updated')->after('TEST3');
            $builder->delete('TEST1');
            $builder->move('TEST4')->before('TEST3');
        });
        $content = $adapter->read();

        $this->assertEquals('TEST4="escaped value"'.PHP_EOL.'TEST3=value3'.PHP_EOL.'TEST2=updated', $content);
    }

    /** @test */
    public function can_extend_builder()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $adapter->setSource('INTEGER_VALUE=111');
        $methodsBefore = $dotenv->builder()->methods();
        try {
            $dotenv->extend('increment', Increment::class);
        } catch (InvalidArgumentException $e) {
            $this->fail('Could not extend manager');
        }
        $methodsAfter = $dotenv->builder()->methods();

        $dotenv->increment('INTEGER_VALUE');
        $content = $adapter->read();

        $this->assertArrayNotHasKey('increment', $methodsBefore);
        $this->assertArrayHasKey('increment', $methodsAfter);
        $this->assertEquals('INTEGER_VALUE=112', $content);
    }

    /** @test */
    public function throws_an_exception_when_extending_with_a_non_class()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $this->expectException(InvalidArgumentException::class);

        $dotenv->extend('name', 'no class');
    }

    /** @test */
    public function throws_an_exception_when_extending_with_a_class_that_does_not_implement_change()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $this->expectException(InvalidArgumentException::class);

        $dotenv->extend('name', InvalidBuilderChange::class);
    }

    /** @test */
    public function can_get_the_builder_instance_from_the_manager()
    {
        $adapter = new FakeDotenvAdapter;
        $dotenv = new DotenvManager($adapter);
        $result = $dotenv->builder();

        $this->assertInstanceOf(DotenvBuilder::class, $result);
    }
}

class Increment extends Change
{
    use HasKey;

    /**
     * Increment constructor.
     * @param string $key
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Apply the pending change to the given content.
     *
     * @param $content
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

class InvalidManagerChange
{

}
