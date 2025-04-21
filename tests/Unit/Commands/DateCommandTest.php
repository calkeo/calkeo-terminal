<?php

use App\Commands\DateCommand;
use App\Livewire\Terminal;
use Tests\TestCase;

class DateCommandTest extends TestCase
{
    public function test_date_command_displays_current_date_and_time_in_various_formats()
    {
        $command = new DateCommand();
        $terminal = new Terminal();
        $output = $command->execute($terminal);

        // Check header
        $this->assertStringContainsString('Current Date and Time:', $output[0]);
        $this->assertStringContainsString('text-cyan-400', $output[0]); // header style

        // Check standard format
        $this->assertStringContainsString('Standard:', $output[3]);
        $this->assertStringContainsString('text-pink-400', $output[3]); // value style

        // Check ISO format
        $this->assertStringContainsString('ISO 8601:', $output[4]);
        $this->assertStringContainsString('text-pink-400', $output[4]); // value style

        // Check Unix timestamp
        $this->assertStringContainsString('Unix Time:', $output[5]);
        $this->assertStringContainsString('text-pink-400', $output[5]); // value style

        // Check additional formats header
        $this->assertStringContainsString('Additional Formats:', $output[7]);
        $this->assertStringContainsString('text-yellow-400', $output[7]); // subheader style

        // Check full format
        $this->assertStringContainsString('Full:', $output[8]);
        $this->assertStringContainsString('text-pink-400', $output[8]); // value style

        // Check short format
        $this->assertStringContainsString('Short:', $output[9]);
        $this->assertStringContainsString('text-pink-400', $output[9]); // value style

        // Check time format
        $this->assertStringContainsString('Time:', $output[10]);
        $this->assertStringContainsString('text-pink-400', $output[10]); // value style

        // Verify that the output contains valid date formats
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $output[4]); // ISO format
        $this->assertMatchesRegularExpression('/\d{1,2}\/\d{1,2}\/\d{4}/', $output[9]); // Short format
        $this->assertMatchesRegularExpression('/\d{2}:\d{2}:\d{2}/', $output[10]); // Time format
    }
}
