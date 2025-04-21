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
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    public function set(string $key, $value): void
    {
        $this->state[$key] = $value;
    }

    /**
     * Get a state variable
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->state[$key] ?? $default;
    }

    /**
     * Check if a state variable exists
     *
     * @param  string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->state[$key]);
    }

    /**
     * Remove a state variable
     *
     * @param  string $key
     * @return void
     */
    public function remove(string $key): void
    {
        unset($this->state[$key]);
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
