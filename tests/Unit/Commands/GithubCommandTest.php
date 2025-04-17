<?php

namespace Tests\Unit\Commands;

use App\Commands\GithubCommand;
use Tests\TestCase;

class GithubCommandTest extends TestCase
{
    protected $command;

    protected function setUp(): void
    {
        parent::setUp();

        $this->command = new GithubCommand();
    }

    /**
     * Test that the command name is correct
     */
    public function test_command_name_is_correct()
    {
        $this->assertEquals('git', $this->command->getName());
    }

    /**
     * Test that the command description is correct
     */
    public function test_command_description_is_correct()
    {
        $this->assertEquals('Displays git info', $this->command->getDescription());
    }

    /**
     * Test that the command has the correct aliases
     */
    public function test_command_has_correct_aliases()
    {
        $this->assertEquals(['github'], $this->command->getAliases());
    }

    /**
     * Test that the command returns the expected output
     */
    public function test_command_returns_expected_output()
    {
        $output = $this->command->execute();

        // Check that the output is not empty
        $this->assertNotEmpty($output);

        // Check that the output contains the GitHub URL
        $this->assertStringContainsString('https://github.com/calkeo', implode("\n", $output));

        // Check that the output contains the Git style information
        $this->assertStringContainsString('remote.origin.url', implode("\n", $output));
        $this->assertStringContainsString('remote.origin.fetch', implode("\n", $output));
        $this->assertStringContainsString('remote.origin.pushurl', implode("\n", $output));
        $this->assertStringContainsString('remote.origin.push', implode("\n", $output));

        // Check that the output contains the clickable link
        $this->assertStringContainsString('Visit my GitHub profile:', implode("\n", $output));
        $this->assertStringContainsString('<a href=', implode("\n", $output));
    }
}
