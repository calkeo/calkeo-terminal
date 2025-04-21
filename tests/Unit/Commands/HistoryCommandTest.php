<?php

use App\Commands\HistoryCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class HistoryCommandTest extends TestCase
{
    protected $command;
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new HistoryCommand();
        $this->terminal = new Terminal();
        Session::forget('command_history');
    }

    public function test_history_command_displays_empty_message_when_no_history_exists()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertCount(1, $output);
        $this->assertStringContainsString('No command history available.', $output[0]);
    }

    public function test_history_command_displays_command_history_with_timestamps()
    {
        // Add some test history
        $history = [
            [
                'command' => 'help',
                'timestamp' => '2024-01-01 12:00:00',
            ],
            [
                'command' => 'echo Hello World',
                'timestamp' => '2024-01-01 12:01:00',
            ],
        ];
        Session::put('command_history', $history);

        $output = $this->command->execute($this->terminal);

        $this->assertCount(5, $output);
        $this->assertStringContainsString('Command History', $output[0]);
        $this->assertEquals('================', $output[1]);
        $this->assertEquals('', $output[2]);
        $this->assertStringContainsString('1', $output[3]);
        $this->assertStringContainsString('2024-01-01 12:00:00', $output[3]);
        $this->assertStringContainsString('help', $output[3]);
        $this->assertStringContainsString('2', $output[4]);
        $this->assertStringContainsString('2024-01-01 12:01:00', $output[4]);
        $this->assertStringContainsString('echo Hello World', $output[4]);
    }
}
