<?php

namespace App\Commands;

use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;

class HistoryCommand extends AbstractCommand
{
    protected $name = 'history';
    protected $description = 'Display command history with timestamps';

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        $output = [];
        $history = Session::get('command_history', []);

        if (empty($history)) {
            return [
                $this->formatOutput("No command history available.", 'warning'),
            ];
        }

        $output[] = $this->formatOutput("Command History", 'header');
        $output[] = "================";
        $output[] = "";

        foreach ($history as $index => $entry) {
            $timestamp = $entry['timestamp'] ?? 'Unknown';
            $command = $entry['command'] ?? '';

            $output[] = sprintf(
                "%s %s %s",
                $this->formatOutput(str_pad($index + 1, 4), 'value'),
                $this->formatOutput($timestamp, 'info'),
                $this->formatOutput($command, 'command')
            );
        }

        return $output;
    }
}
