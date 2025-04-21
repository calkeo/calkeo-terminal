<?php

namespace App\Commands;

use Illuminate\Support\Facades\Session;

class WhoamiCommand extends AbstractCommand
{
    protected $name = 'whoami';
    protected $description = 'Display the current username';
    protected $hidden = true;

    public function execute(array $args = []): array
    {
        $username = Session::get('terminal_username', 'guest');
        $hostname = 'calkeo.dev';
        $uid = rand(1000, 9999);
        $gid = rand(100, 999);
        $groups = 'users,admin,docker';
        $home = '/home/' . $username;
        $shell = '/bin/bash';

        $output = [];

        // Basic username output (like traditional whoami)
        $output[] = $username;

        // Add detailed user information (like id command)
        $output[] = '';
        $output[] = $this->formatOutput("User Information:", 'header');
        $output[] = "uid=" . $this->formatOutput($uid, 'value') . "(" . $this->formatOutput($username, 'value') . ") gid=" . $this->formatOutput($gid, 'value') . "(users) groups=" . $this->formatOutput($groups, 'value');
        $output[] = "home=" . $this->formatOutput($home, 'path') . " shell=" . $this->formatOutput($shell, 'path');
        $output[] = "host=" . $this->formatOutput($hostname, 'value');

        return $output;
    }
}
