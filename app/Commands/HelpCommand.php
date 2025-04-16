<?php

namespace App\Commands;

class HelpCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'help';
        $this->description = 'Show available commands';
    }

    /**
     * Execute the command
     *
     * @param  array   $args
     * @return array
     */
    public function execute(array $args = []): array
    {
        $output = [];
        $output[] = $this->formatOutput('Available Commands:', 'info');
        $output[] = '';

        // Get command registry from the terminal component
        $registry = app(CommandRegistry::class);

        foreach ($registry->getHelp() as $line) {
            $output[] = $line;
        }

        $output[] = '';
        $output[] = $this->formatOutput('For more information about a specific command, use: help <command>', 'info');

        return $output;
    }
}
