<?php

namespace MarkWalet\DotenvManager\Tests;

use MarkWalet\DotenvManager\Changes\Change;
use MarkWalet\DotenvManager\Changes\Concerns\HasKey;
use MarkWalet\DotenvManager\DotenvBuilder;
use MarkWalet\DotenvManager\Exceptions\InvalidArgumentException;
use MarkWalet\DotenvManager\Exceptions\MethodNotFoundException;
use PHPUnit\Framework\TestCase;

class DotenvBuilderTest extends TestCase
{
    /** @test */
    public function can_extend_builder()
    {
        $builder = new DotenvBuilder;
        $builder->extend('decrement', Decrement::class);
        $original = "INTEGER_VALUE=144";
        $builder->decrement('INTEGER_VALUE');

        $new = $builder->apply($original);

        $this->assertEquals("INTEGER_VALUE=143", $new);
    }

    /** @test */
    public function throws_an_exception_when_extending_with_a_non_class()
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new DotenvBuilder;

        $builder->extend('name', 'no class');
    }

    /** @test */
    public function throws_an_exception_when_extending_with_a_class_that_does_not_implement_change()
    {
        $this->expectException(InvalidArgumentException::class);
        $builder = new DotenvBuilder;

        $builder->extend('name', InvalidBuilderChange::class);
    }

    /** @test */
    public function throws_an_exception_when_calling_a_method_that_is_not_registered()
    {
        $this->expectException(MethodNotFoundException::class);
        $builder = new DotenvBuilder;

        $builder->nonExistingMethod();
    }
}

class Decrement extends Change
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

        $replacement = $this->getKey().'='.($value - 1);

        return preg_replace($search, $replacement, $content);
    }
}

class InvalidBuilderChange
{
}
