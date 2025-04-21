<?php

namespace Tests\Traits;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Commands\CommandState;
use App\Livewire\Terminal;

trait TerminalTestTrait
{
    protected function initializeTerminal(): Terminal
    {
        $registry = new CommandRegistry();
        $parser = new CommandParser();
        $terminal = new Terminal();

        // Initialize the Terminal component manually
        $terminal->boot($registry, $parser);

        return $terminal;
    }
}
