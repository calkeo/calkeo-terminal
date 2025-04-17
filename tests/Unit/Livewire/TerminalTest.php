<?php

namespace App\Tests\Unit\Livewire;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use Mockery;
use Tests\TestCase;

class TerminalTest extends TestCase
{
    protected $terminal;
    protected $commandRegistry;
    protected $commandParser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create mocks for dependencies
        $this->commandRegistry = Mockery::mock(CommandRegistry::class);
        $this->commandParser = Mockery::mock(CommandParser::class);

        // Mock the Session facade
        Session::shouldReceive('get')->with('command_history', [])->andReturn([]);
        Session::shouldReceive('put')->andReturn(null);

        // Mock the CommandParser parse method
        $this->commandParser->shouldReceive('parse')
             ->with('help')
             ->andReturn([
                 'command' => 'help',
                 'args' => [],
             ]);

        // Mock the CommandRegistry get method
        $this->commandRegistry->shouldReceive('get')
             ->with('help')
             ->andReturn(null);

        // Create the terminal component
        $this->terminal = new Terminal();

        // Manually set the dependencies
        $this->terminal->boot($this->commandRegistry, $this->commandParser);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Test that showSuggestions is set to false when executing a command
     */
    public function test_show_suggestions_is_hidden_after_command_execution()
    {
        // Set showSuggestions to true initially
        $this->terminal->showSuggestions = true;

        // Set a command
        $this->terminal->command = 'help';

        // Execute the command
        $this->terminal->executeCommand();

        // Verify that showSuggestions is now false
        $this->assertFalse($this->terminal->showSuggestions);
    }
}