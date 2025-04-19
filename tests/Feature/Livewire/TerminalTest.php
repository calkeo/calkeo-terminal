<?php

namespace Tests\Feature\Livewire;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TerminalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Session::put('terminal_logged_in', true);
        Session::put('terminal_username', 'testuser');
    }

    #[Test]
    public function test_it_can_mount_the_terminal()
    {
        $component = Livewire::test(Terminal::class);

        $component->assertSet('command', '')
                  ->assertSet('username', 'testuser');

        // Assert that output contains exactly one item (the welcome message)
        $this->assertCount(1, $component->get('output'));
    }

    #[Test]
    public function test_it_displays_error_messages_on_failure()
    {
        $component = Livewire::test(Terminal::class);

        // Simulate an error by executing an invalid command
        $component->set('command', 'invalid_command')
                  ->call('executeCommand');

        // Get the last two messages from output
        $output = $component->get('output');
        $lastMessages = array_slice($output, -2);

        // Assert the error messages are present
        $this->assertContains(
            '<span class="text-red-400">Command not found: invalid_command</span>',
            $lastMessages
        );
        $this->assertContains(
            '<span class="text-yellow-400">Type \'help\' to see available commands.</span>',
            $lastMessages
        );
    }

    #[Test]
    public function test_it_does_not_execute_empty_commands()
    {
        $component = Livewire::test(Terminal::class)
            ->set('command', '')
            ->call('executeCommand');

        $component->assertSet('command', '');
    }

    #[Test]
    public function test_it_handles_exceptions_gracefully()
    {
        $component = Livewire::test(Terminal::class);

        // Mock the CommandRegistry to throw an exception
        $this->mock(CommandRegistry::class, function ($mock) {
            $mock->shouldReceive('get')
                 ->andThrow(new \Exception('Test exception'));
        });

        // Execute a command that will trigger the exception
        $component->set('command', 'test_command')
                  ->call('executeCommand');

        // Get the output messages
        $output = $component->get('output');
        $errorMessages = array_slice($output, -2);

        // Assert the error messages are present
        $this->assertContains(
            '<span class="text-red-400">Oops! Something went wrong and my creator has been notified.</span>',
            $errorMessages
        );
        $this->assertContains(
            '<span class="text-red-400">Rest assured, he WILL be disciplined.</span>',
            $errorMessages
        );
    }

}