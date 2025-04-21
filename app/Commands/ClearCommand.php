<?php

namespace App\Commands;

use App\Livewire\Terminal;

class ClearCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'clear';
        $this->description = 'Clear the terminal screen';
    }

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        // Set the clear state in the terminal's command state
        $terminal->getCommandState()->set('clear', true);
        return [];
    }
}
