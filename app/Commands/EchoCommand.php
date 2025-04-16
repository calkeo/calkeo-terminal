<?php

namespace App\Commands;

class EchoCommand extends AbstractCommand
{
    protected $name = 'echo';
    protected $description = 'Display a line of text that is passed as an argument';

    public function execute(array $args = []): array
    {
        if (empty($args)) {
            return [
                $this->formatOutput("Usage: echo <text>", 'warning'),
                $this->formatOutput("Example: echo Hello, World!", 'info'),
            ];
        }

        // Join all arguments with spaces, preserving quoted strings
        $text = implode(' ', $args);

        // Remove surrounding quotes if present
        $text = trim($text, '"\'');

        return [
            $this->formatOutput($text, 'default'),
        ];
    }
}
