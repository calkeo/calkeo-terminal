<?php

namespace Tests\Unit\Commands;

use App\Commands\GlobalThermonuclearWarCommand;
use App\Livewire\Terminal;
use Tests\TestCase;

class GlobalThermonuclearWarCommandTest extends TestCase
{
    protected $command;
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new GlobalThermonuclearWarCommand();
        $this->terminal = new Terminal();
    }

    public function test_command_returns_expected_output()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertEquals('delayed', $output[0]['type']);
        $this->assertEquals(0, $output[0]['delay']);
        $this->assertStringContainsString('Global Thermonuclear War', $output[0]['content']);

        $this->assertEquals('delayed', $output[1]['type']);
        $this->assertEquals(0, $output[1]['delay']);
        $this->assertStringContainsString('=========================', $output[1]['content']);

        $this->assertEquals('delayed', $output[2]['type']);
        $this->assertEquals(1500, $output[2]['delay']);
        $this->assertStringContainsString('Greetings Professor Falken.', $output[2]['content']);
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('globalthermonuclearwar', $this->command->getName());
        $this->assertEquals('Play Global Thermonuclear War', $this->command->getDescription());
    }

    public function test_command_has_correct_aliases()
    {
        $aliases = $this->command->getAliases();
        $this->assertContains('gtw', $aliases);
        $this->assertContains('war', $aliases);
    }

    public function test_command_is_hidden()
    {
        $this->assertTrue($this->command->isHidden());
    }
}
