<?php

use App\Commands\HistoryCommand;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class HistoryCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up the session facade
        $this->app->instance('session', new \Illuminate\Session\SessionManager($this->app));
    }

    public function test_history_command_displays_empty_message_when_no_history_exists()
    {
        // Clear any existing history
        Session::forget('command_history');

        $command = new HistoryCommand();
        $output = $command->execute();

        $this->assertCount(1, $output);
        $this->assertStringContainsString('No command history available', $output[0]);
        $this->assertStringContainsString('text-yellow-400', $output[0]); // warning style
    }

    public function test_history_command_displays_command_history_with_timestamps()
    {
        // Set up test history
        $history = [
            [
                'command' => 'help',
                'timestamp' => '2023-01-01 12:00:00',
            ],
            [
                'command' => 'echo Hello World',
                'timestamp' => '2023-01-01 12:01:00',
            ],
            [
                'command' => 'date',
                'timestamp' => '2023-01-01 12:02:00',
            ],
        ];

        Session::put('command_history', $history);

        $command = new HistoryCommand();
        $output = $command->execute();

        // Check header
        $this->assertStringContainsString('Command History', $output[0]);
        $this->assertStringContainsString('text-cyan-400', $output[0]); // header style

        // Check each history entry
        $this->assertCount(6, $output); // Header + separator + empty line + 3 entries

        // Check first entry
        $this->assertStringContainsString('1', $output[3]);
        $this->assertStringContainsString('2023-01-01 12:00:00', $output[3]);
        $this->assertStringContainsString('help', $output[3]);

        // Check second entry
        $this->assertStringContainsString('2', $output[4]);
        $this->assertStringContainsString('2023-01-01 12:01:00', $output[4]);
        $this->assertStringContainsString('echo Hello World', $output[4]);

        // Check third entry
        $this->assertStringContainsString('3', $output[5]);
        $this->assertStringContainsString('2023-01-01 12:02:00', $output[5]);
        $this->assertStringContainsString('date', $output[5]);
    }
}
