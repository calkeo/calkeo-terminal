<?php

namespace App\Commands;

class SudoCommand extends AbstractCommand
{
    protected $name = 'sudo';
    protected $description = 'Superuser do (requires elevation)';
    protected $hidden = true;

    public function execute(array $args = []): array
    {
        // Check for the rm -rf pattern
        if (count($args) >= 2 && $args[0] === 'rm' && $args[1] === '-rf') {
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