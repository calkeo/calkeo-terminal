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
     * Hidden command
     *
     * @var bool
     */
    protected $hidden = false;

    /**
     * Command aliases
     *
     * @var array
     */
    protected $aliases = [];

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
     * Check if the command is hidden
     *
     * @return bool
     */
    public function isHidden(): bool
    {
        return $this->hidden;
    }

    /**
     * Get command aliases
     *
     * @return array
     */
    public function getAliases(): array
    {
        return $this->aliases;
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
            'normal' => 'text-gray-400',
            'white' => 'text-white',
        ];

        $class = $styles[$style] ?? $styles['default'];

        return "<span class=\"{$class}\">{$text}</span>";
    }

    protected function lineBreak(): string
    {
        return '<br>';
    }

    /**
     * Create a styled box container
     *
     * @param  array   $lines Lines to include in the box
     * @param  ?string $title Optional title for the box
     * @return string  HTML for the styled box
     */
    protected function createStyledBox(array $lines, ?string $title = null): string
    {
        $boxContainer = $this->formatOutput(
            '<div class="border border-gray-700 overflow-hidden">' .
            ($title ? '<div class="bg-gray-800 p-2 border-b border-gray-700"><span class="text-gray-400">$</span> ' . $title . '</div>' : '') .
            '<div class="p-4 space-y-1">' . implode('<br>', $lines) . '</div>' .
            '</div>',
            'info'
        );

        return $boxContainer;
    }

    /**
     * Create a styled table
     *
     * @param  array  $headers Table headers
     * @param  array  $rows    Table rows
     * @return string HTML for the styled table
     */
    protected function createStyledTable(array $headers, array $rows): string
    {
        $html = '<div class="border border-gray-700 my-2">';

        // Headers
        $html .= '<div class="bg-gray-800 px-2 py-1 border-b border-gray-700 flex">';
        foreach ($headers as $index => $header) {
            $html .= '<div class="' . ($index > 0 ? 'ml-8' : '') . ' ' . ($index === 0 ? 'w-32' : 'flex-1') . '">';
            $html .= $this->formatOutput($header, 'subheader');
            $html .= '</div>';
        }
        $html .= '</div>';

        // Rows
        $html .= '<div class="p-2">';
        foreach ($rows as $row) {
            $html .= '<div class="py-0.5 flex">';
            foreach ($row as $index => $cell) {
                $html .= '<div class="' . ($index > 0 ? 'ml-8' : '') . ' ' . ($index === 0 ? 'w-32' : 'flex-1') . '">' . $cell . '</div>';
            }
            $html .= '</div>';
        }
        $html .= '</div>';

        $html .= '</div>';

        return $html;
    }
}
