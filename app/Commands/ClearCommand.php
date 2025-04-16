<?php

namespace App\Commands;

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
     * @param  array   $args
     * @return array
     */
    public function execute(array $args = []): array
    {
        // Return a special marker that the terminal component will recognize
        return ['__CLEAR__'];
    }
}
