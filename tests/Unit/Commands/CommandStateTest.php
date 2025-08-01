<?php

namespace Tests\Unit\Commands;

use App\Commands\CommandState;
use App\Commands\CommandStates;
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
        $this->commandState->set(CommandStates::CLEAR, 'value');
        $this->assertEquals('value', $this->commandState->get(CommandStates::CLEAR));
    }

    public function test_get_default_value()
    {
        $this->assertEquals('default', $this->commandState->get(CommandStates::CLEAR, 'default'));
    }

    public function test_has_state()
    {
        $this->assertFalse($this->commandState->has(CommandStates::CLEAR));

        $this->commandState->set(CommandStates::CLEAR, 'value');
        $this->assertTrue($this->commandState->has(CommandStates::CLEAR));
    }

    public function test_remove_state()
    {
        $this->commandState->set(CommandStates::CLEAR, 'value');
        $this->assertTrue($this->commandState->has(CommandStates::CLEAR));

        $this->commandState->remove(CommandStates::CLEAR);
        $this->assertFalse($this->commandState->has(CommandStates::CLEAR));
    }

    public function test_clear_state()
    {
        $this->commandState->set(CommandStates::CLEAR, 'value1');
        $this->commandState->set(CommandStates::LOGOUT, 'value2');

        $this->assertCount(2, $this->commandState->all());

        $this->commandState->clear();
        $this->assertCount(0, $this->commandState->all());
    }

    public function test_all_state()
    {
        $this->commandState->set(CommandStates::CLEAR, 'value1');
        $this->commandState->set(CommandStates::LOGOUT, 'value2');

        $all = $this->commandState->all();

        $this->assertCount(2, $all);
        $this->assertEquals('value1', $all[CommandStates::CLEAR->value]);
        $this->assertEquals('value2', $all[CommandStates::LOGOUT->value]);
    }
}
