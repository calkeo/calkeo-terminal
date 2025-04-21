<?php

namespace App\Commands;

use App\Livewire\Terminal;

class GlobalThermonuclearWarCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'globalthermonuclearwar';
        $this->description = 'Play Global Thermonuclear War';
        $this->aliases = ['gtw', 'war'];
        $this->hidden = true;
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
        $output = [];

        $output[] = [
            'type' => 'delayed',
            'delay' => 0,
            'content' => $this->formatOutput('Global Thermonuclear War', 'subheader'),
        ];
        $output[] = [
            'type' => 'delayed',
            'delay' => 0,
            'content' => $this->formatOutput('=========================', 'subheader'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 1500,
            'content' => $this->formatOutput("Greetings Professor Falken.", 'white'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 3000,
            'content' => $this->formatOutput("A strange game. The only winning move is not to play.", 'white'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 3000,
            'content' => $this->formatOutput("How about a nice game of chess?", 'success'),
        ];

        return $output;
    }
}
