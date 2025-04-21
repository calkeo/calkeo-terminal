<?php

namespace Tests\Unit\Commands;

use App\Commands\ChangelogCommand;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ChangelogCommandTest extends TestCase
{
    protected $command;
    protected $changelogPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new ChangelogCommand();
        $this->changelogPath = base_path('CHANGELOG.md');

        // Create a temporary changelog file for testing
        if (!File::exists($this->changelogPath)) {
            File::put($this->changelogPath, $this->getSampleChangelog());
        }
    }

    protected function tearDown(): void
    {
        // Clean up the temporary changelog file
        if (File::exists($this->changelogPath)) {
            File::delete($this->changelogPath);
        }

        parent::tearDown();
    }

    /**
     * Test that the command name is correct
     */
    public function test_command_name_is_correct()
    {
        $this->assertEquals('changelog', $this->command->getName());
    }

    /**
     * Test that the command description is correct
     */
    public function test_command_description_is_correct()
    {
        $this->assertEquals('Display the changelog', $this->command->getDescription());
    }

    /**
     * Test that the command returns the changelog content
     */
    public function test_command_returns_changelog_content()
    {
        $output = $this->command->execute();

        // Check that the output is not empty
        $this->assertNotEmpty($output);

        // Check that the output contains the changelog content
        $this->assertStringContainsString('Changelog', implode("\n", $output));
        $this->assertStringContainsString('Unreleased', implode("\n", $output));
        $this->assertStringContainsString('Added', implode("\n", $output));
    }

    /**
     * Test that the changelog file exists
     */
    public function test_changelog_exists()
    {
        $this->assertTrue(File::exists($this->changelogPath));
    }

    /**
     * Test that the command formats version headers correctly
     */
    public function test_command_formats_version_headers()
    {
        $output = $this->command->execute();
        $outputText = implode("\n", $output);

        // Check that version headers are formatted with the 'value' style
        $this->assertStringContainsString('class="text-pink-400"', $outputText);
        $this->assertStringContainsString('[1.0.0]', $outputText);
    }

    /**
     * Test that the command adds spacing between entries
     */
    public function test_command_adds_spacing()
    {
        $output = $this->command->execute();

        // Check that there are div elements with margin classes
        $hasMarginDivs = false;
        foreach ($output as $line) {
            if (strpos($line, '<div class="my-') !== false) {
                $hasMarginDivs = true;
                break;
            }
        }

        $this->assertTrue($hasMarginDivs, 'The output should contain div elements with margin classes');
    }

    /**
     * Get a sample changelog content for testing
     */
    protected function getSampleChangelog(): string
    {
        return <<<EOT
# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial changelog functionality
- `changelog` command to display version history

## [1.0.0] - 2023-01-01

### Added
- Initial release
- Terminal interface
- Command system
- Login functionality
EOT;
    }
}
