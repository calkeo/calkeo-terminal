<?php

namespace Tests\Unit\Commands;

use App\Commands\PrivacyCommand;
use PHPUnit\Framework\TestCase;

class PrivacyCommandTest extends TestCase
{
    private PrivacyCommand $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new PrivacyCommand();
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('privacy', $this->command->getName());
        $this->assertEquals('Show the privacy policy of the application', $this->command->getDescription());
    }

    public function test_command_output_contains_required_sections()
    {
        $output = $this->command->execute();

        // Convert output array to string for easier searching
        $outputString = implode('', $output);

        // Check for main sections
        $this->assertStringContainsString('PRIVACY POLICY', $outputString);
        $this->assertStringContainsString('Last Updated:', $outputString);
        $this->assertStringContainsString('1. INTRODUCTION', $outputString);
        $this->assertStringContainsString('2. INFORMATION WE COLLECT', $outputString);
        $this->assertStringContainsString('3. HOW I USE YOUR INFORMATION', $outputString);
        $this->assertStringContainsString('4. DISCLOSURE OF YOUR INFORMATION', $outputString);
        $this->assertStringContainsString('5. SECURITY OF YOUR INFORMATION', $outputString);
        $this->assertStringContainsString('6. DATA RETENTION', $outputString);
        $this->assertStringContainsString('7. CHILDREN\'S PRIVACY', $outputString);
        $this->assertStringContainsString('8. CHANGES TO THIS PRIVACY POLICY', $outputString);
        $this->assertStringContainsString('9. CONTACT ME', $outputString);
    }

    public function test_command_output_contains_contact_information()
    {
        $output = $this->command->execute();
        $outputString = implode('', $output);

        $this->assertStringContainsString('mail@calkeo.dev', $outputString);
    }

    public function test_command_output_contains_data_retention_information()
    {
        $output = $this->command->execute();
        $outputString = implode('', $output);

        $this->assertStringContainsString('Any data collected during your session', $outputString);
        $this->assertStringContainsString('2 hours of inactivity', $outputString);
    }
}
