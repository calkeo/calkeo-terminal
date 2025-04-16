<?php

namespace App\Commands;

interface CommandInterface
{
    /**
     * Get the command name
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get the command description
     *
     * @return string
     */
    public function getDescription(): string;

    /**
     * Execute the command
     *
     * @param  array $args  Command arguments
     * @return array Output lines
     */
    public function execute(array $args = []): array;

    /**
     * Get command usage information
     *
     * @return string
     */
    public function getUsage(): string;
}
