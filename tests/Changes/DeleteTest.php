<?php

namespace MarkWalet\DotenvManager\Tests\Changes;

use MarkWalet\DotenvManager\Changes\Delete;
use PHPUnit\Framework\TestCase;

class DeleteTest extends TestCase
{
    /** @test */
    public function can_set_key_and_value_through_constructor()
    {
        $change = new Delete('EXISTING_KEY');

        $this->assertEquals('EXISTING_KEY', $change->getKey());
    }

    /** @test */
    public function can_apply_to_content_string()
    {
        $change = new Delete('EXISTING_KEY');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'EXISTING_KEY=oldValue'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_delete_key_at_start_of_string()
    {
        $change = new Delete('EXISTING_KEY');
        $original = 'EXISTING_KEY=oldValue'.PHP_EOL.'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_delete_key_in_middle_of_string()
    {
        $change = new Delete('EXISTING_KEY');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'EXISTING_KEY=oldValue'.PHP_EOL.'TEST_VALUE2=example2';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }

    /** @test */
    public function can_delete_key_at_end_of_string()
    {
        $change = new Delete('EXISTING_KEY');
        $original = 'TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2'.PHP_EOL.'EXISTING_KEY=oldValue';

        $new = $change->apply($original);

        $this->assertEquals('TEST_VALUE1=example1'.PHP_EOL.'TEST_VALUE2=example2', $new);
    }
}
