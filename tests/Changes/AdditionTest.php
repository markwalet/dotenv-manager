<?php

namespace MarkWalet\DotenvManager\Tests\Changes;

use MarkWalet\DotenvManager\Changes\Addition;
use PHPUnit\Framework\TestCase;

class AdditionTest extends TestCase
{
    /** @test */
    public function can_set_key_and_value_through_constructor()
    {
        $change = new Addition('NEW_KEY', 'value');

        $this->assertEquals('NEW_KEY', $change->getKey());
        $this->assertEquals('value', $change->getValue());
    }

    /** @test */
    public function value_is_optional_in_constructor()
    {
        $change = new Addition('NEW_KEY');

        $this->assertEquals('NEW_KEY', $change->getKey());
        $this->assertEquals('null', $change->getValue());
    }

    /** @test */
    public function formats_boolean_to_string()
    {
        $changeFalse = new Addition('NEW_KEY', false);
        $changeTrue = new Addition('NEW_KEY', true);

        $this->assertEquals('false', $changeFalse->getValue());
        $this->assertEquals('true', $changeTrue->getValue());
    }

    /** @test */
    public function formats_null_to_string()
    {
        $change = new Addition('NEW_KEY', null);

        $this->assertEquals('null', $change->getValue());
    }

    /** @test */
    public function encapsulates_string_with_quotes_when_value_contains_spaces()
    {
        $change = new Addition('NEW_KEY', 'Value with spaces');

        $this->assertEquals('"Value with spaces"', $change->getValue());
    }

    /** @test */
    public function can_change_value_after_construction()
    {
        $change = new Addition('NEW_KEY', 'value');

        $change->value('Updated');

        $this->assertEquals('Updated', $change->getValue());
    }

    /** @test */
    public function can_apply_to_content_string()
    {
        $change = new Addition('NEW_KEY', 'newValue');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'NEW_KEY=newValue', $new);
    }

    /** @test */
    public function can_choose_to_set_new_value_after_an_other_key()
    {
        $change = new Addition('NEW_KEY', 'newValue');
        $change->after('TEST_VALUE1');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'NEW_KEY=newValue'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_choose_to_set_new_value_before_an_other_key()
    {
        $change = new Addition('NEW_KEY', 'newValue');
        $change->before('TEST_VALUE2');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'NEW_KEY=newValue'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }
}
