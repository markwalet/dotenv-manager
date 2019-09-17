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
    /**
     * @var FakeDotenvAdapter
     */
    private $adapter;

    /**
     * @var DotenvManager
     */
    private $dotenv;


    /**
     * This method is called before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->adapter = new FakeDotenvAdapter;
        $this->dotenv = new DotenvManager($this->adapter);
    }

    /** @test */
    public function can_add_a_line_to_dotenv()
    {
        $this->adapter->setSource("TEST1=value");

        $this->dotenv->add('TEST2', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value".PHP_EOL."TEST2=value2", $content);
    }

    /** @test */
    public function can_create_a_line_in_dotenv()
    {
        $this->adapter->setSource("TEST1=value");

        $this->dotenv->create('TEST2', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value".PHP_EOL."TEST2=value2", $content);
    }

    /** @test */
    public function can_set_a_line_in_dotenv()
    {
        $this->adapter->setSource("TEST1=value");

        $this->dotenv->set('TEST1', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value2", $content);
    }

    /** @test */
    public function can_update_a_line_in_dotenv()
    {
        $this->adapter->setSource("TEST1=value");

        $this->dotenv->update('TEST1', 'value2');
        $content = $this->adapter->read();

        $this->assertEquals("TEST1=value2", $content);
    }

    /** @test */
    public function can_delete_a_line_in_dotenv()
    {
        $this->adapter->setSource("TEST1=value".PHP_EOL."TEST2=value2");

        $this->dotenv->delete('TEST1');
        $content = $this->adapter->read();

        $this->assertEquals("TEST2=value2", $content);
    }

    /** @test */
    public function can_unset_a_line_in_dotenv()
    {
        $this->adapter->setSource("TEST1=value".PHP_EOL."TEST2=value2");

        $this->dotenv->unset('TEST1');
        $content = $this->adapter->read();

        $this->assertEquals("TEST2=value2", $content);
    }

    /** @test */
    public function can_mutate_multiple_lines_at_once_in_dotenv()
    {
        $this->adapter->setSource("TEST1=value1".PHP_EOL."TEST2=value2".PHP_EOL."TEST3=value3");

        $this->dotenv->mutate(function (DotenvBuilder $builder) {
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
        $methodsBefore = $this->dotenv->builder()->methods();
        try {
            $this->dotenv->extend('increment', Increment::class);
        } catch (InvalidArgumentException $e) {
            $this->fail('Could not extend manager');
        }
        $methodsAfter = $this->dotenv->builder()->methods();

        $this->dotenv->increment('INTEGER_VALUE');
        $content = $this->adapter->read();

        $this->assertArrayNotHasKey('increment', $methodsBefore);
        $this->assertArrayHasKey('increment', $methodsAfter);
        $this->assertEquals("INTEGER_VALUE=112", $content);
    }

    /** @test */
    public function throws_an_exception_when_extending_with_a_non_class()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->dotenv->extend('name', 'no class');
    }

    /** @test */
    public function throws_an_exception_when_extending_with_a_class_that_does_not_implement_change()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->dotenv->extend('name', InvalidBuilderChange::class);
    }

    /** @test */
    public function can_get_the_builder_instance_from_the_manager()
    {
        $result = $this->dotenv->builder();

        $this->assertInstanceOf(DotenvBuilder::class, $result);
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
