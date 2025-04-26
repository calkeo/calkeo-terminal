<?php

namespace Tests\Unit\Commands;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Mockery;
use Tests\TestCase;

class CommandCompletionTest extends TestCase
{
    protected $terminal;
    protected $commandRegistry;
    protected $commandParser;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock command registry
        $this->commandRegistry = $this->app->make(CommandRegistry::class);

        // Create a command parser
        $this->commandParser = $this->app->make(CommandParser::class);

        // Create the terminal component with a mock stream method
        $this->terminal = new class extends Terminal
        {
            public function stream($to, $content, $replace = false)
            {
                // Do nothing in tests
                return null;
            }
        };

        // Manually set the dependencies
        $this->terminal->boot($this->commandRegistry, $this->commandParser);

        // Initialize the terminal
        $this->terminal->mount();
    }

    /**
     * Test that regular commands complete immediately
     */
    public function test_regular_commands_complete_immediately()
    {
        // Run a regular command (help)
        $this->terminal->command = 'help';
        $this->terminal->executeCommand();

        // Check that the command completed (currentCommandName is null)
        $this->assertNull($this->terminal->currentCommandName);

        // Run another command to verify we can execute new commands
        $this->terminal->command = 'clear';
        $this->terminal->executeCommand();

        // Check that the second command also completed
        $this->assertNull($this->terminal->currentCommandName);
    }

    /**
     * Test that interactive commands stay interactive until complete
     */
    public function test_interactive_commands_stay_interactive()
    {
        // Start the contact command
        $this->terminal->command = 'contact';
        $this->terminal->executeCommand();

        // Check that the command is still active (interactive)
        $this->assertEquals('contact', $this->terminal->currentCommandName);

        // Provide subject input
        $this->terminal->command = 'Test Subject';
        $this->terminal->executeCommand();

        // Command should still be interactive
        $this->assertEquals('contact', $this->terminal->currentCommandName);

        // Confirm subject
        $this->terminal->command = 'yes';
        $this->terminal->executeCommand();

        // Command should still be interactive
        $this->assertEquals('contact', $this->terminal->currentCommandName);

        // Provide message
        $this->terminal->command = 'Test Message';
        $this->terminal->executeCommand();

        // Command should still be interactive
        $this->assertEquals('contact', $this->terminal->currentCommandName);

        // Confirm message (completes the command)
        $this->terminal->boot($this->commandRegistry, $this->commandParser);
        $this->terminal->command = 'yes';
        $this->terminal->executeCommand();

        // Now the command should be complete
        $this->assertNull($this->terminal->currentCommandName);
    }

    /**
     * Test that we can run a new command after an interactive command completes
     */
    public function test_can_run_new_command_after_interactive_command_completes()
    {
        // Start and complete the contact command
        $this->terminal->command = 'contact';
        $this->terminal->executeCommand();

        $this->terminal->command = 'Test Subject';
        $this->terminal->executeCommand();

        $this->terminal->command = 'yes';
        $this->terminal->executeCommand();

        $this->terminal->command = 'Test Message';
        $this->terminal->executeCommand();

        $this->terminal->boot($this->commandRegistry, $this->commandParser);

        $this->terminal->command = 'yes';
        $this->terminal->executeCommand();

        // Verify the command is complete
        $this->assertNull($this->terminal->currentCommandName);

        // Clear output before running new command
        $this->terminal->output = [];

        // Run a new command
        $this->terminal->command = 'help';
        $this->terminal->executeCommand();

        // Verify the new command executed properly
        $this->assertNull($this->terminal->currentCommandName);
        $this->assertStringContainsString('calkeOS Terminal Help', implode("\n", $this->terminal->output));
    }

    /**
     * Test that we can cancel an interactive command and run a new one
     */
    public function test_can_cancel_interactive_command_and_run_new_one()
    {
        // Start the contact command
        $this->terminal->command = 'contact';
        $this->terminal->executeCommand();

        // Verify it's interactive
        $this->assertEquals('contact', $this->terminal->currentCommandName);

        // Clear the session to simulate cancellation
        Session::forget(['contact_step', 'contact_subject', 'contact_message']);
        $this->terminal->currentCommandName = null;

        // Clear output before running new command
        $this->terminal->output = [];

        // Run a new command
        $this->terminal->boot($this->commandRegistry, $this->commandParser);
        $this->terminal->command = 'help';
        $this->terminal->executeCommand();

        // Verify the new command executed properly
        $this->assertNull($this->terminal->currentCommandName);
        $this->assertStringContainsString('calkeOS Terminal Help', implode("\n", $this->terminal->output));
    }
}
