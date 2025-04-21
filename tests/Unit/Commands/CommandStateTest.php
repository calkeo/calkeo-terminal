<?php

namespace Tests\Unit\Commands;

use App\Commands\CommandState;
use Tests\TestCase;

class CommandStateTest extends TestCase
{
    protected $commandState;

    protected function setUp(): void
    {
        parent::setUp();
        $this->commandState = new CommandState();
    }

    public function test_set_and_get_state()
    {
        $this->commandState->set('test', 'value');
        $this->assertEquals('value', $this->commandState->get('test'));
    }

    public function test_get_default_value()
    {
        $this->assertEquals('default', $this->commandState->get('nonexistent', 'default'));
    }

    public function test_has_state()
    {
        $this->assertFalse($this->commandState->has('nonexistent'));

        $this->commandState->set('test', 'value');
        $this->assertTrue($this->commandState->has('test'));
    }

    public function test_remove_state()
    {
        $this->commandState->set('test', 'value');
        $this->assertTrue($this->commandState->has('test'));

        $this->commandState->remove('test');
        $this->assertFalse($this->commandState->has('test'));
    }

    public function test_clear_state()
    {
        $this->commandState->set('test1', 'value1');
        $this->commandState->set('test2', 'value2');

        $this->assertCount(2, $this->commandState->all());

        $this->commandState->clear();
        $this->assertCount(0, $this->commandState->all());
    }

    public function test_all_state()
    {
        $this->commandState->set('test1', 'value1');
        $this->commandState->set('test2', 'value2');

        $all = $this->commandState->all();

        $this->assertCount(2, $all);
        $this->assertEquals('value1', $all['test1']);
        $this->assertEquals('value2', $all['test2']);
    }
}
