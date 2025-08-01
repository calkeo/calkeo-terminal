<?php

namespace App\Commands;

use App\Commands\CommandStates;
use App\Livewire\Terminal;

class LogoutCommand extends AbstractCommand
{
    protected $name = 'logout';
    protected $description = 'Logout from the terminal or use "exit" as an alias';
    protected $aliases = ['exit'];

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        session()->forget('terminal_logged_in');
        $terminal->getCommandState()->set(CommandStates::LOGOUT, true);
        return [
            "Logging out...",
            "Thanks for using calkeOS Terminal!",
            "Redirecting to login...",
        ];
    }
}
