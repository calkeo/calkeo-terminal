<?php

namespace App\Commands;

class LogoutCommand extends AbstractCommand
{
    protected $name = 'logout';
    protected $description = 'Logout from the terminal or use "exit" as an alias';
    protected $aliases = ['exit'];

    public function execute(array $args = []): array
    {
        session()->forget('terminal_logged_in');
        return [
            "Logging out...",
            "Thanks for using calkeOS Terminal!",
            "Redirecting to login...",
            "__LOGOUT__", // Special marker for the frontend to handle redirect
        ];
    }
}