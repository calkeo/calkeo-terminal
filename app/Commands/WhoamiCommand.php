<?php

namespace App\Commands;

class WhoamiCommand extends AbstractCommand
{
    protected $name = 'whoami';
    protected $description = 'Display the current username';

    public function execute(array $args = []): array
    {
        $username = session('terminal_username', 'guest');
        return [$username];
    }
}