<?php

use App\Commands\EchoCommand;
use App\Livewire\Terminal;
use Tests\TestCase;

class EchoCommandTest extends TestCase
{
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->terminal = new Terminal();
    }

    public function test_echo_command_displays_usage_when_no_arguments_provided()
    {
        $command = new EchoCommand();
        $output = $command->execute($this->terminal);

        $this->assertCount(2, $output);
        $this->assertStringContainsString('Usage: echo <text>', $output[0]);
        $this->assertStringContainsString('Example: echo Hello, World!', $output[1]);
    }

    public function test_echo_command_displays_text_when_arguments_provided()
    {
        $command = new EchoCommand();
        $output = $command->execute($this->terminal, ['Hello', 'World']);

        $this->assertCount(1, $output);
        $this->assertStringContainsString('Hello World', $output[0]);
    }

    public function test_echo_command_handles_quoted_strings()
    {
        $command = new EchoCommand();
        $output = $command->execute($this->terminal, ['"Hello', 'World"']);

        $this->assertCount(1, $output);
        $this->assertStringContainsString('Hello World', $output[0]);
    }

    public function test_echo_command_handles_single_quoted_strings()
    {
        $command = new EchoCommand();
        $output = $command->execute($this->terminal, ["'Hello", "World'"]);

        $this->assertCount(1, $output);
        $this->assertStringContainsString('Hello World', $output[0]);
    }

    public function test_echo_command_handles_multiple_arguments_with_spaces()
    {
        $command = new EchoCommand();
        $output = $command->execute($this->terminal, ['Hello', 'beautiful', 'World']);

        $this->assertCount(1, $output);
        $this->assertStringContainsString('Hello beautiful World', $output[0]);
    }
}
