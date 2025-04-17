<?php

namespace App\Commands;

use App\Commands\CommandRegistry;

class HelpCommand extends AbstractCommand
{
    protected $name = 'help';
    protected $description = 'Show help information';
    protected $commandRegistry;

    /**
     * Constructor
     */
    public function __construct(CommandRegistry $commandRegistry)
    {
        $this->commandRegistry = $commandRegistry;
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

        // If a specific command is requested
        if (!empty($args)) {
            $commandName = $args[0];
            $command = $this->commandRegistry->get($commandName);

            if ($command) {
                $output[] = $this->formatOutput("Command: " . $command->getName(), 'header');

                $boxLines = [
                    $this->formatOutput("Description:", 'subheader') . " " . $command->getDescription(),
                    $this->formatOutput("Usage:", 'subheader') . " " . $command->getUsage(),
                ];

                $output[] = $this->createStyledBox($boxLines, "Command Details");
                return $output;
            } else {
                $output[] = $this->formatOutput("Command not found: " . $commandName, 'error');
                return $output;
            }
        }

        // General help
        $output[] = $this->formatOutput("calkeOS Terminal Help", 'header');

        // Create table for commands
        $headers = ['Command', 'Description'];
        $rows = [];

        $commands = collect($this->commandRegistry->all())->sortBy(function ($command) {
            return $command->getName();
        })->all();
        $commandCount = 0;

        foreach ($commands as $command) {
            if (!$command->isHidden()) {
                $commandCount++;
                $rows[] = [
                    $this->formatOutput($command->getName(), 'command'),
                    $command->getDescription(),
                ];
            }
        }

        if ($commandCount === 0) {
            $rows[] = [
                $this->formatOutput("No commands available.", 'warning'),
                "",
            ];
        }

        $output[] = $this->createStyledTable($headers, $rows);
        $output[] = "";
        $output[] = $this->formatOutput("For more information on a specific command, type: ", 'info') . $this->formatOutput("help &lt;command&gt;", 'command');

        return $output;
    }
}