<?php

namespace MarkWalet\DotenvManager\Tests\Changes;

use MarkWalet\DotenvManager\Changes\Move;
use MarkWalet\DotenvManager\Exceptions\InvalidArgumentException;
use MarkWalet\DotenvManager\Exceptions\InvalidPositionException;
use PHPUnit\Framework\TestCase;

class MoveTest extends TestCase
{
    /** @test */
    public function can_set_key_through_constructor()
    {
        $change = new Move('EXISTING_KEY');

        $this->assertEquals('EXISTING_KEY', $change->getKey());
    }

    /** @test */
    public function can_move_key_at_start_of_string()
    {
        $change = new Move('EXISTING_KEY');
        $change->after('TEST_VALUE2');
        $original = 'EXISTING_KEY=value'.PHP_EOL.'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=value', $new);
    }

    /** @test */
    public function can_move_key_at_end_of_string()
    {
        $change = new Move('EXISTING_KEY');
        $change->before('TEST_VALUE1');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=value';

        $new = $change->apply($original);

        $this->assertEquals('EXISTING_KEY=value'.PHP_EOL.'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_move_key_in_middle_of_string()
    {
        $change = new Move('EXISTING_KEY');
        $change->after('TEST_VALUE2');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'EXISTING_KEY=value'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=value', $new);
    }

    /** @test */
    public function throws_invalid_argument_exception_when_key_is_not_found()
    {
        $change = new Move('NON_EXISTING');
        $change->after('TEST_VALUE1');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';

        $this->expectException(InvalidArgumentException::class);

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function throws_invalid_position_exception_when_no_new_position_is_given()
    {
        $this->expectException(InvalidPositionException::class);
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';
        $change = new Move('TEST_VALUE1');

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }
}
