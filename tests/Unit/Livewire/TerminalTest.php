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

    public function test_terminal_detects_delayed_output()
    {
        $component = Livewire::test(Terminal::class);

        // Test with delayed output
        $delayedResult = [
            ['type' => 'delayed', 'content' => 'First delayed message', 'delay' => 1000],
            ['type' => 'delayed', 'content' => 'Second delayed message', 'delay' => 1000],
        ];

        // Use reflection to access the protected method
        $reflection = new \ReflectionClass($component->instance());
        $method = $reflection->getMethod('isDelayedResponse');
        $method->setAccessible(true);

        $this->assertTrue($method->invoke($component->instance(), $delayedResult));

        // Test with regular output
        $regularResult = [
            'Regular output line 1',
            'Regular output line 2',
        ];

        $this->assertFalse($method->invoke($component->instance(), $regularResult));

        // Test with mixed output
        $mixedResult = [
            'Regular output line',
            ['type' => 'delayed', 'content' => 'Delayed message', 'delay' => 1000],
        ];

        $this->assertTrue($method->invoke($component->instance(), $mixedResult));
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
        $this->assertFalse($component->get('isProcessingDelayedOutput'));

        // Execute command
        $component->set('command', 'test');
        $component->call('executeCommand');

        // The component should have called the delayedOutput method via JavaScript
        // We can't directly test this in a unit test, but we can verify the command was executed
        $this->assertNotEmpty($component->get('output'));
    }

    public function test_terminal_processes_delayed_output()
    {
        $component = Livewire::test(Terminal::class);

        // Initial state
        $this->assertFalse($component->get('isProcessingDelayedOutput'));

        // Process delayed output
        $delayedResult = [
            ['type' => 'delayed', 'content' => 'First delayed message', 'delay' => 100],
            ['type' => 'delayed', 'content' => 'Second delayed message', 'delay' => 100],
            'Regular output line',
        ];

        $component->call('delayedOutput', $delayedResult);

        // Verify output was processed
        $this->assertFalse($component->get('isProcessingDelayedOutput'));

        // The output array will include the welcome message plus our 3 new lines
        $output = $component->get('output');
        $this->assertGreaterThanOrEqual(3, count($output));

        // Check that our messages are in the output
        $this->assertStringContainsString('First delayed message', implode('', $output));
        $this->assertStringContainsString('Second delayed message', implode('', $output));
        $this->assertStringContainsString('Regular output line', implode('', $output));
    }

    public function test_wrap_line_content_formats_output_correctly()
    {
        $component = Livewire::test(Terminal::class);

        // Use reflection to access the protected method
        $reflection = new \ReflectionClass($component->instance());
        $method = $reflection->getMethod('wrapLineContent');
        $method->setAccessible(true);

        $content = 'Test content with <span>HTML</span>';
        $wrapped = $method->invoke($component->instance(), $content);

        $this->assertEquals(
            "<div class='whitespace-pre-wrap leading-relaxed'>Test content with <span>HTML</span></div>",
            $wrapped
        );
    }
}
