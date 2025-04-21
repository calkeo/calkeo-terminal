<?php

namespace Tests\Unit\Commands;

use App\Commands\GithubCommand;
use App\Livewire\Terminal;
use Tests\TestCase;

class GithubCommandTest extends TestCase
{
    protected $command;
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new GithubCommand();
        $this->terminal = new Terminal();
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
        $aliases = $this->command->getAliases();
        $this->assertContains('github', $aliases);
    }

    /**
     * Test that the command returns the expected output
     */
    public function test_command_returns_expected_output()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('remote.origin.url', $output[0]);
        $this->assertStringContainsString('https://github.com/calkeo', $output[0]);
        $this->assertStringContainsString('Visit my GitHub profile:', $output[5]);
        $this->assertStringContainsString('<a href="https://github.com/calkeo"', $output[6]);
    }
}
