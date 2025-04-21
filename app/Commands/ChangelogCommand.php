<?php

namespace App\Commands;

use App\Livewire\Terminal;

class ChangelogCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'changelog';
        $this->description = 'Display the changelog';
    }

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        $output = [];

        // Path to the changelog file
        $changelogPath = base_path('resources/files/CHANGELOG.md');

        // Check if the file exists
        if (!file_exists($changelogPath)) {
            $output[] = $this->formatOutput('Error: Changelog file not found.', 'error');
            return $output;
        }

        // Read the changelog file
        $changelogContent = file_get_contents($changelogPath);

        // Parse the markdown content
        $lines = explode("\n", $changelogContent);

        // Process each line and apply appropriate styling
        $inVersion = false;
        $inSection = false;

        foreach ($lines as $line) {
            // Headers (##)
            if (preg_match('/^##\s+(.+)$/', $line, $matches)) {
                // Add spacing before version headers
                $output[] = '<div class="my-4"></div>'; // Add margin with HTML

                // Format the entire version header with a different color
                $output[] = $this->formatOutput($matches[1], 'value');

                $inVersion = true;
                $inSection = false;
                continue;
            }

            // Subheaders (###)
            if (preg_match('/^###\s+(.+)$/', $line, $matches)) {
                // Add spacing before subheaders
                $output[] = '<div class="my-2"></div>'; // Add margin with HTML
                $output[] = $this->formatOutput($matches[1], 'subheader');
                $inSection = true;
                continue;
            }

            // List items
            if (preg_match('/^-\s+(.+)$/', $line, $matches)) {
                // Add spacing between list items
                $output[] = '<div class="my-1">- ' . $this->formatOutput($matches[1], 'info') . '</div>';
                continue;
            }

            // Links
            if (preg_match('/\[([^\]]+)\]\(([^)]+)\)/', $line, $matches)) {
                $line = str_replace(
                    $matches[0],
                    $this->formatOutput($matches[1], 'path') . ' (' . $matches[2] . ')',
                    $line
                );
            }

            // Empty lines
            if (trim($line) === '') {
                $output[] = '<div class="my-1"></div>'; // Add margin with HTML
                continue;
            }

            // Regular text
            $output[] = $line;
        }

        return $output;
    }
}
