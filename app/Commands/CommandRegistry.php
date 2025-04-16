<?php

namespace App\Commands;

use Illuminate\Support\Collection;

class CommandRegistry
{
    /**
     * Registered commands
     *
     * @var Collection
     */
    protected $commands;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commands = new Collection();
    }

    /**
     * Register a command
     *
     * @param  CommandInterface $command
     * @return void
     */
    public function register(CommandInterface $command): void
    {
        $this->commands->put($command->getName(), $command);
    }

    /**
     * Get a command by name
     *
     * @param  string                  $name
     * @return CommandInterface|null
     */
    public function get(string $name): ?CommandInterface
    {
        return $this->commands->get($name);
    }

    /**
     * Check if a command exists
     *
     * @param  string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return $this->commands->has($name);
    }

    /**
     * Get all registered commands
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->commands;
    }

    /**
     * Get command help information
     *
     * @return array
     */
    public function getHelp(): array
    {
        $help = [];

        foreach ($this->commands as $command) {
            $help[] = sprintf(
                "%-15s %s",
                $command->getName(),
                $command->getDescription()
            );
        }

        return $help;
    }
}
