<?php

namespace App\Commands;

class DateCommand extends AbstractCommand
{
    protected $name = 'date';
    protected $description = 'Display the current date and time';

    public function execute(array $args = []): array
    {
        $output = [];
        $now = new \DateTime();

        // Default format
        $output[] = $this->formatOutput("Current Date and Time:", 'header');
        $output[] = "==================";
        $output[] = "";

        // Standard format
        $output[] = sprintf(
            "Standard: %s",
            $this->formatOutput($now->format('D M j H:i:s T Y'), 'value')
        );

        // ISO format
        $output[] = sprintf(
            "ISO 8601: %s",
            $this->formatOutput($now->format('c'), 'value')
        );

        // Unix timestamp
        $output[] = sprintf(
            "Unix Time: %s",
            $this->formatOutput($now->getTimestamp(), 'value')
        );

        // Custom formats
        $output[] = "";
        $output[] = $this->formatOutput("Additional Formats:", 'subheader');
        $output[] = sprintf(
            "Full: %s",
            $this->formatOutput($now->format('l, F j, Y g:i:s A T'), 'value')
        );
        $output[] = sprintf(
            "Short: %s",
            $this->formatOutput($now->format('d/m/Y'), 'value')
        );
        $output[] = sprintf(
            "Time: %s",
            $this->formatOutput($now->format('H:i:s'), 'value')
        );
        $output[] = sprintf(
            "Wrong: %s",
            $this->formatOutput($now->format('m/d/Y'), 'value')
        );

        return $output;
    }
}
