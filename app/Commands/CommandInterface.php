<?php

namespace App\Commands;

use App\Livewire\Terminal;

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
     * @param  Terminal $terminal Terminal instance
     * @param  array    $args     Command arguments
     * @return array    Output lines
     */
    public function execute(Terminal $terminal, array $args = []): array;

    /**
     * Get command usage information
     *
     * @return string
     */
    public function getUsage(): string;

    /**
     * Get command aliases
     *
     * @return array
     */
    public function getAliases(): array;
}
