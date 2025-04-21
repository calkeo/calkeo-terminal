<?php

namespace App\Commands;

class VersionCommand extends AbstractCommand
{
    protected $name = 'version';
    protected $description = 'Show the version of the application';
    protected $aliases = ['v', 'ver', '-v', '--version'];
    protected $hidden = true;

    public function execute(array $args = []): array
    {
        $output = [];

        $output[] = "Version: " . config('app.version');

        return $output;
    }
}
