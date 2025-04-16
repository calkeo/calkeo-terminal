<?php

namespace App\Commands\Traits;

use Illuminate\Support\Facades\Session;

trait InteractiveCommandTrait
{
    /**
     * Get the current step from session
     *
     * @return int
     */
    protected function getCurrentStep(): int
    {
        return Session::get($this->getStepKey(), 1);
    }

    /**
     * Set the current step
     *
     * @param  int    $step
     * @return void
     */
    protected function setCurrentStep(int $step): void
    {
        Session::put($this->getStepKey(), $step);
    }

    /**
     * Get a session value
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    protected function getSessionValue(string $key, $default = null)
    {
        return Session::get($this->getSessionKey($key), $default);
    }

    /**
     * Set a session value
     *
     * @param  string $key
     * @param  mixed  $value
     * @return void
     */
    protected function setSessionValue(string $key, $value): void
    {
        Session::put($this->getSessionKey($key), $value);
    }

    /**
     * Clear all session data for this command
     *
     * @return void
     */
    protected function clearSession(): void
    {
        foreach ($this->getSessionKeys() as $key) {
            Session::forget($this->getSessionKey($key));
        }
        Session::forget($this->getStepKey());
    }

    /**
     * Get the session key for a given key
     *
     * @param  string   $key
     * @return string
     */
    protected function getSessionKey(string $key): string
    {
        return sprintf('%s_%s', $this->getName(), $key);
    }

    /**
     * Get the step key for session storage
     *
     * @return string
     */
    protected function getStepKey(): string
    {
        return sprintf('%s_step', $this->getName());
    }

    /**
     * Get all session keys used by this command
     *
     * @return array
     */
    abstract protected function getSessionKeys(): array;

    /**
     * Handle the current step
     *
     * @param  array   $args
     * @param  int     $step
     * @return array
     */
    abstract protected function handleStep(array $args, int $step): array;

    /**
     * Start the interactive process
     *
     * @return array
     */
    abstract protected function startInteractiveProcess(): array;
}
