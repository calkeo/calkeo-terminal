<?php

namespace App\Commands;

class CommandParser
{
    /**
     * Parse command input
     *
     * @param  string  $input
     * @return array
     */
    public function parse(string $input): array
    {
        // Trim whitespace
        $input = trim($input);

        // Split by spaces, but preserve quoted strings
        $parts = $this->splitCommand($input);

        // First part is the command name
        $command = $parts[0] ?? '';

        // Rest are arguments
        $args = array_slice($parts, 1);

        return [
            'command' => $command,
            'args' => $args,
        ];
    }

    /**
     * Split command string into parts, preserving quoted strings
     *
     * @param  string  $input
     * @return array
     */
    protected function splitCommand(string $input): array
    {
        $parts = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';

        for ($i = 0; $i < strlen($input); $i++) {
            $char = $input[$i];

            // Handle quotes
            if (($char === '"' || $char === "'") && ($i === 0 || $input[$i - 1] !== '\\')) {
                if (!$inQuotes) {
                    $inQuotes = true;
                    $quoteChar = $char;
                } elseif ($quoteChar === $char) {
                    $inQuotes = false;
                } else {
                    $current .= $char;
                }
                continue;
            }

            // Handle spaces
            if ($char === ' ' && !$inQuotes) {
                if ($current !== '') {
                    $parts[] = $current;
                    $current = '';
                }
                continue;
            }

            $current .= $char;
        }

        // Add the last part if there is one
        if ($current !== '') {
            $parts[] = $current;
        }

        return $parts;
    }
}
