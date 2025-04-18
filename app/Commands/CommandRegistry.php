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
     * Command aliases
     *
     * @var Collection
     */
    protected $aliases;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->commands = new Collection();
        $this->aliases = new Collection();

        // Register default commands
        $this->register(new HelpCommand($this));
        $this->register(new ClearCommand());
        $this->register(new DateCommand());
        $this->register(new EchoCommand());
        $this->register(new WhoamiCommand());
        $this->register(new GithubCommand());
        $this->register(new AboutCommand());
        $this->register(new ChangelogCommand());
        $this->register(new CalculatorCommand());
        $this->register(new SudoCommand());
        $this->register(new SshCommand());
        $this->register(new GamesCommand());
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

        // Register aliases
        foreach ($command->getAliases() as $alias) {
            $this->aliases->put($alias, $command->getName());
        }
    }

    /**
     * Get a command by name
     *
     * @param  string                  $name
     * @return CommandInterface|null
     */
    public function get(string $name): ?CommandInterface
    {
        // Check if the name is an alias
        if ($this->aliases->has($name)) {
            $commandName = $this->aliases->get($name);
            return $this->commands->get($commandName);
        }

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
        return $this->commands->has($name) || $this->aliases->has($name);
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
            $aliases = $command->getAliases();
            $aliasesStr = !empty($aliases) ? ' (aliases: ' . implode(', ', $aliases) . ')' : '';

            $help[] = sprintf(
                "%-15s %s%s",
                $command->getName(),
                $command->getDescription(),
                $aliasesStr
            );
        }

        return $help;
    }
}
