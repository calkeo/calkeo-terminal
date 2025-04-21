<?php

namespace App\Commands;

use App\Livewire\Terminal;

class SudoCommand extends AbstractCommand
{
    protected $name = 'sudo';
    protected $description = 'Superuser do (requires elevation)';
    protected $hidden = true;

    public function execute(Terminal $terminal, array $args = []): array
    {
        // Check for the rm -rf pattern
        if (count($args) >= 2 && $args[0] === 'rm' && $args[1] === '-rf') {
            $terminal->js('setTimeout(() => window.open("https://www.youtube.com/watch?v=dQw4w9WgXcQ", "_blank"), 2000);');

            return [
                $this->formatOutput("Nice try! But I like my files where they are.", 'warning'),
                $this->formatOutput("Permission denied: You don't have the authority to delete my existence!", 'error'),
            ];
        }

        return [
            $this->formatOutput("Permission denied: You are not in the sudoers file.", 'error'),
            $this->formatOutput("This incident will be reported.", 'warning'),
        ];
    }
}
