<?php

namespace App\Commands;

abstract class AbstractCommand implements CommandInterface
{
    /**
     * Command name
     *
     * @var string
     */
    protected $name;

    /**
     * Command description
     *
     * @var string
     */
    protected $description;

    /**
     * Get the command name
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Get the command description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Get command usage information
     *
     * @return string
     */
    public function getUsage(): string
    {
        return $this->name;
    }

    /**
     * Format output with proper styling
     *
     * @param  string $text     Text to format
     * @param  string $style    Style to apply (success, error, info, warning, command, path, value)
     * @return string Formatted text
     */
    protected function formatOutput(string $text, string $style = 'default'): string
    {
        $styles = [
            'default' => 'text-green-400',
            'success' => 'text-emerald-400',
            'error' => 'text-red-400',
            'info' => 'text-cyan-400',
            'warning' => 'text-yellow-400',
            'command' => 'text-purple-400',
            'path' => 'text-blue-400',
            'value' => 'text-pink-400',
            'header' => 'text-cyan-400 font-bold',
            'subheader' => 'text-yellow-400 font-semibold',
        ];

        $class = $styles[$style] ?? $styles['default'];

        return "<span class=\"{$class}\">{$text}</span>";
    }
}
