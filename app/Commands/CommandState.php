<?php

namespace App\Commands;

/**
 * CommandState class for managing state between commands and the terminal component
 */
class CommandState
{
    /**
     * State variables
     *
     * @var array
     */
    protected array $state = [];

    /**
     * Set a state variable
     *
     * @param  CommandStates $key
     * @param  mixed         $value
     * @return void
     */
    public function set(CommandStates $key, $value): void
    {
        $this->state[$key->value] = $value;
    }

    /**
     * Get a state variable
     *
     * @param  CommandStates $key
     * @param  mixed         $default
     * @return mixed
     */
    public function get(CommandStates $key, $default = null)
    {
        return $this->state[$key->value] ?? $default;
    }

    /**
     * Check if a state variable exists
     *
     * @param  CommandStates $key
     * @return bool
     */
    public function has(CommandStates $key): bool
    {
        return isset($this->state[$key->value]);
    }

    /**
     * Remove a state variable
     *
     * @param  CommandStates $key
     * @return void
     */
    public function remove(CommandStates $key): void
    {
        unset($this->state[$key->value]);
    }

    /**
     * Clear all state variables
     *
     * @return void
     */
    public function clear(): void
    {
        $this->state = [];
    }

    /**
     * Get all state variables
     *
     * @return array
     */
    public function all(): array
    {
        return $this->state;
    }
}
