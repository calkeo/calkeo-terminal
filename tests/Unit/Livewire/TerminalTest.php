<?php

namespace Tests\Unit\Livewire;

use App\Commands\AbstractCommand;
use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use Mockery;
use Tests\TestCase;

class TestDelayedCommand extends AbstractCommand
{
    protected $name = 'test-delayed';
    protected $description = 'A test command with delayed output';

    public function execute(array $args = []): array
    {
        return [
            ['text' => 'Immediate output', 'style' => 'text-green-500'],
            ['text' => 'Delayed output 1', 'style' => 'text-blue-500', 'delay' => 500],
            ['text' => 'Delayed output 2', 'style' => 'text-yellow-500', 'delay' => 500],
        ];
    }
}

class TerminalTest extends TestCase
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

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_show_suggestions_is_hidden_after_command_execution()
    {
        $registry = new CommandRegistry();
        $parser = new CommandParser();

        $component = Livewire::test(Terminal::class, [
            'commandRegistry' => $registry,
            'commandParser' => $parser,
        ]);

        $component->set('showSuggestions', true);
        $component->set('command', 'help');
        $component->call('executeCommand');

        $this->assertFalse($component->get('showSuggestions'));
    }

    public function test_terminal_handles_delayed_output()
    {
        // Create a mock command that returns delayed output
        $mockCommand = Mockery::mock(AbstractCommand::class);
        $mockCommand->shouldReceive('execute')
                    ->once()
                    ->andReturn([
                        ['type' => 'delayed', 'content' => 'First delayed message', 'delay' => 1000],
                        ['type' => 'delayed', 'content' => 'Second delayed message', 'delay' => 1000],
                    ]);

        // Create mock registry that returns our mock command
        $mockRegistry = Mockery::mock(CommandRegistry::class);
        $mockRegistry->shouldReceive('get')
                     ->with('test')
                     ->andReturn($mockCommand);
        $mockRegistry->shouldReceive('all')
                     ->andReturn(collect(['test' => $mockCommand]));

        // Create mock parser
        $mockParser = Mockery::mock(CommandParser::class);
        $mockParser->shouldReceive('parse')
                   ->with('test')
                   ->andReturn(['command' => 'test', 'args' => []]);

        $this->app->instance(CommandRegistry::class, $mockRegistry);
        $this->app->instance(CommandParser::class, $mockParser);

        Session::put('terminal_logged_in', true);
        Session::put('terminal_username', 'test');

        $component = Livewire::test('terminal');

        // Initial state
        $this->assertEmpty($component->get('delayedOutput'));
        $this->assertFalse($component->get('isProcessingDelayedOutput'));

        // Execute command
        $component->set('command', 'test');
        $component->call('executeCommand');

        // Verify delayed output is being processed
        $this->assertTrue($component->get('isProcessingDelayedOutput'));
        $this->assertCount(1, $component->get('delayedOutput'));
    }

    public function test_terminal_disables_input_during_delayed_output()
    {
        // Create a mock command that returns delayed output
        $mockCommand = Mockery::mock(AbstractCommand::class);
        $mockCommand->shouldReceive('execute')
                    ->once()
                    ->andReturn([
                        ['type' => 'delayed', 'content' => 'First message', 'delay' => 1000],
                        ['type' => 'delayed', 'content' => 'Second message', 'delay' => 1000],
                    ]);

        // Create mock registry
        $mockRegistry = Mockery::mock(CommandRegistry::class);
        $mockRegistry->shouldReceive('get')
                     ->with('test')
                     ->andReturn($mockCommand);
        $mockRegistry->shouldReceive('all')
                     ->andReturn(collect(['test' => $mockCommand]));

        // Create mock parser
        $mockParser = Mockery::mock(CommandParser::class);
        $mockParser->shouldReceive('parse')
                   ->with('test')
                   ->andReturn(['command' => 'test', 'args' => []]);

        $this->app->instance(CommandRegistry::class, $mockRegistry);
        $this->app->instance(CommandParser::class, $mockParser);

        $component = Livewire::test('terminal');

        // Initial state - input should be enabled
        $this->assertFalse($component->get('isProcessingDelayedOutput'));
        $this->assertNull($component->get('nextDelayedOutputTime'));

        // Execute command
        $component->set('command', 'test');
        $component->call('executeCommand');

        // After execution, delayed output should be set up
        $this->assertCount(1, $component->get('delayedOutput'));
        $this->assertTrue($component->get('isProcessingDelayedOutput'));
        $this->assertNotNull($component->get('nextDelayedOutputTime'));

        // Process first delayed output
        $component->call('processNextDelayedOutput');

        // Should still be processing with one message left
        $this->assertTrue($component->get('isProcessingDelayedOutput'));
        $this->assertNotNull($component->get('nextDelayedOutputTime'));
        $this->assertCount(0, $component->get('delayedOutput'));

        // Process second delayed output
        $component->call('processNextDelayedOutput');

        // Should be done processing
        $this->assertFalse($component->get('isProcessingDelayedOutput'));
        $this->assertNull($component->get('nextDelayedOutputTime'));
        $this->assertEmpty($component->get('delayedOutput'));
    }
}
