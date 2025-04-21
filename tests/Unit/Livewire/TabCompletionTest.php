<?php

namespace Tests\Unit\Livewire;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Commands\NumberGuessingCommand;
use App\Commands\RockPaperScissorsCommand;
use App\Livewire\Terminal;
use Livewire\Livewire;
use Tests\TestCase;

class TabCompletionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Start the session
        $this->startSession();

        // Set up session data
        session([
            'command_history' => [],
            'terminal_username' => 'testuser',
            'terminal_logged_in' => true,
        ]);
    }

    public function test_hidden_commands_are_not_shown_in_tab_completion()
    {
        $registry = new CommandRegistry();
        $parser = new CommandParser();

        // Register some hidden commands
        $registry->register(new RockPaperScissorsCommand());
        $registry->register(new NumberGuessingCommand());

        $component = Livewire::test(Terminal::class, [
            'commandRegistry' => $registry,
            'commandParser' => $parser,
        ]);

        // Trigger tab completion with empty command
        $component->call('handleTabCompletion');

        // Get the suggestions
        $suggestions = $component->get('suggestions');

        // Verify that hidden commands are not in the suggestions
        $this->assertNotContains('rps', $suggestions);
        $this->assertNotContains('numberguess', $suggestions);
    }

    public function test_hidden_commands_are_not_shown_in_partial_tab_completion()
    {
        $registry = new CommandRegistry();
        $parser = new CommandParser();

        // Register some hidden commands
        $registry->register(new RockPaperScissorsCommand());
        $registry->register(new NumberGuessingCommand());

        $component = Livewire::test(Terminal::class, [
            'commandRegistry' => $registry,
            'commandParser' => $parser,
        ]);

        // Set a partial command that could match hidden commands
        $component->set('command', 'r');
        $component->call('handleTabCompletion');

        // Get the suggestions
        $suggestions = $component->get('suggestions');

        // Verify that hidden commands are not in the suggestions
        $this->assertNotContains('rps', $suggestions);
    }

    public function test_hidden_commands_are_not_shown_in_exact_match_tab_completion()
    {
        $registry = new CommandRegistry();
        $parser = new CommandParser();

        // Register some hidden commands
        $registry->register(new RockPaperScissorsCommand());
        $registry->register(new NumberGuessingCommand());

        $component = Livewire::test(Terminal::class, [
            'commandRegistry' => $registry,
            'commandParser' => $parser,
        ]);

        // Set the exact command name
        $component->set('command', 'rps');
        $component->call('handleTabCompletion');

        // Verify that the command was not auto-completed
        $this->assertEquals('rps', $component->get('command'));
        $this->assertFalse($component->get('showSuggestions'));
    }
}
