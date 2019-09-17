<?php

namespace MarkWalet\DotenvManager\Tests\Changes;

use MarkWalet\DotenvManager\Changes\Update;
use PHPUnit\Framework\TestCase;

class UpdateTest extends TestCase
{
    /** @test */
    public function can_set_key_and_value_through_constructor()
    {
        $change = new Update('EXISTING_KEY', 'value');

        $this->assertEquals('EXISTING_KEY', $change->getKey());
        $this->assertEquals('value', $change->getValue());
    }

    /** @test */
    public function value_is_optional_in_constructor()
    {
        $change = new Update('EXISTING_KEY');

        $this->assertEquals('EXISTING_KEY', $change->getKey());
        $this->assertEquals('null', $change->getValue());
    }

    /** @test */
    public function formats_boolean_to_string()
    {
        $changeFalse = new Update('EXISTING_KEY', false);
        $changeTrue = new Update('EXISTING_KEY', true);

        $this->assertEquals('false', $changeFalse->getValue());
        $this->assertEquals('true', $changeTrue->getValue());
    }

    /** @test */
    public function formats_null_to_string()
    {
        $change = new Update('EXISTING_KEY', null);

        $this->assertEquals('null', $change->getValue());
    }

    /** @test */
    public function encapsulates_string_with_quotes_when_value_contains_spaces()
    {
        $change = new Update('EXISTING_KEY', 'Value with spaces');

        $this->assertEquals('"Value with spaces"', $change->getValue());
    }

    /** @test */
    public function can_change_value_after_construction()
    {
        $change = new Update('EXISTING_KEY', 'value');

        $change->value('Updated');

        $this->assertEquals('Updated', $change->getValue());
    }

    /** @test */
    public function can_update_key_at_start_of_string()
    {
        $change = new Update('EXISTING_KEY', 'newValue');
        $original = 'EXISTING_KEY=oldValue'.PHP_EOL.'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('EXISTING_KEY=newValue'.PHP_EOL.'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_update_key_at_end_of_string()
    {
        $change = new Update('EXISTING_KEY', 'newValue');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=oldValue';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=newValue', $new);
    }

    /** @test */
    public function can_update_key_in_middle_of_string()
    {
        $change = new Update('EXISTING_KEY', 'newValue');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'EXISTING_KEY=oldValue'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'EXISTING_KEY=newValue'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_move_value_after_an_other_key()
    {
        $change = new Update('EXISTING_KEY', 'newValue');
        $change->after('TEST_VALUE1');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=oldValue';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'EXISTING_KEY=newValue'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_move_value_before_an_other_key()
    {
        $change = new Update('EXISTING_KEY', 'newValue');
        $change->before('TEST_VALUE2');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=oldValue';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'EXISTING_KEY=newValue'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }
}
