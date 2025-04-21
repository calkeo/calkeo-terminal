<?php

namespace App\Commands;

use App\Livewire\Terminal;

class EchoCommand extends AbstractCommand
{
    protected $name = 'echo';
    protected $description = 'Display a line of text that is passed as an argument';

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        if (empty($args)) {
            return [
                $this->formatOutput("Usage: echo <text>", 'warning'),
                $this->formatOutput("Example: echo Hello, World!", 'info'),
            ];
        }

        // Process each argument to remove quotes
        $processedArgs = array_map(function ($arg) {
            return trim($arg, '"\'');
        }, $args);

        // Join arguments with spaces
        $text = implode(' ', $processedArgs);

        return [
            $this->formatOutput($text, 'default'),
        ];
    }
}
