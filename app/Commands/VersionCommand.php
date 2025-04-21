<?php

namespace App\Commands;

use App\Livewire\Terminal;

class VersionCommand extends AbstractCommand
{
    protected $name = 'version';
    protected $description = 'Show the version of the application';
    protected $aliases = ['v', 'ver', '-v', '--version'];
    protected $hidden = true;

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        $output = [];

        $output[] = "Version: " . config('app.version');

        return $output;
    }
}
