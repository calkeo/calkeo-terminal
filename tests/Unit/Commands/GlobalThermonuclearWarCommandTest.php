<?php

namespace Tests\Unit\Commands;

use App\Commands\GlobalThermonuclearWarCommand;
use Tests\TestCase;

class GlobalThermonuclearWarCommandTest extends TestCase
{
    protected $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new GlobalThermonuclearWarCommand();
    }

    public function test_command_returns_expected_output()
    {
        $output = $this->command->execute();

        // Check that we have the expected output for Global Thermonuclear War
        $this->assertEquals('delayed', $output[0]['type']);
        $this->assertEquals(0, $output[0]['delay']);
        $this->assertStringContainsString('Global Thermonuclear War', $output[0]['content']);

        $this->assertEquals('delayed', $output[1]['type']);
        $this->assertEquals(0, $output[1]['delay']);
        $this->assertStringContainsString('=========================', $output[1]['content']);

        $this->assertEquals('delayed', $output[2]['type']);
        $this->assertEquals(1500, $output[2]['delay']);
        $this->assertStringContainsString('Greetings Professor Falken.', $output[2]['content']);

        $this->assertEquals('delayed', $output[3]['type']);
        $this->assertEquals(3000, $output[3]['delay']);
        $this->assertStringContainsString('A strange game. The only winning move is not to play.', $output[3]['content']);

        $this->assertEquals('delayed', $output[4]['type']);
        $this->assertEquals(3000, $output[4]['delay']);
        $this->assertStringContainsString('How about a nice game of chess?', $output[4]['content']);
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('globalthermonuclearwar', $this->command->getName());
        $this->assertEquals('Play Global Thermonuclear War', $this->command->getDescription());
    }

    public function test_command_has_correct_aliases()
    {
        $this->assertContains('gtw', $this->command->getAliases());
        $this->assertContains('war', $this->command->getAliases());
    }

    public function test_command_is_hidden()
    {
        $this->assertTrue($this->command->isHidden());
    }
}
