<?php

namespace App\Commands;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class CommandRegistry
{
    /**
     * Registered commands
     *
     * @var Collection
     */
    public $commands;

    /**
     * Command aliases
     *
     * @var Collection
     */
    public $aliases;

    /**
     * Cache key for commands
     */
    const CACHE_KEY = 'command_registry';

    /**
     * Cache key for aliases
     */
    const ALIASES_CACHE_KEY = 'command_aliases';

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loadFromCache();
    }

    /**
     * Load commands and aliases from cache
     */
    protected function loadFromCache(): void
    {
        $this->commands = Cache::get(self::CACHE_KEY, new Collection());
        $this->aliases = Cache::get(self::ALIASES_CACHE_KEY, new Collection());
    }

    /**
     * Save commands and aliases to cache
     */
    protected function saveToCache(): void
    {
        Cache::forever(self::CACHE_KEY, $this->commands);
        Cache::forever(self::ALIASES_CACHE_KEY, $this->aliases);
    }

    /**
     * Clear the cache
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::ALIASES_CACHE_KEY);
        $this->commands = new Collection();
        $this->aliases = new Collection();
    }

    public static function staticClearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::ALIASES_CACHE_KEY);
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

        $this->saveToCache();
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

    public function resetStaleInteractiveCommands(): bool
    {
        $hasResetInteractiveCommands = false;
        $this->commands->each(function ($command) use (&$hasResetInteractiveCommands) {
            if (in_array(\App\Commands\Traits\InteractiveCommandTrait::class, class_uses($command))) {
                $command->reset();
                $hasResetInteractiveCommands = true;
            }
        });

        return $hasResetInteractiveCommands;
    }
}
