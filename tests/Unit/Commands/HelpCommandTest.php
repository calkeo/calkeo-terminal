<?php

namespace Tests\Unit\Commands;

use App\Commands\CommandInterface;
use App\Commands\CommandRegistry;
use App\Commands\HelpCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class HelpCommandTest extends TestCase
{
    protected $cacheMock;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a fresh cache mock for each test
        $this->cacheMock = Mockery::mock('cache');
        Cache::swap($this->cacheMock);

        // Set default cache behavior
        $this->cacheMock->shouldReceive('get')
             ->with('command_registry', Mockery::type(Collection::class))
             ->byDefault()
             ->andReturn(new Collection());
        $this->cacheMock->shouldReceive('get')
             ->with('command_aliases', Mockery::type(Collection::class))
             ->byDefault()
             ->andReturn(new Collection());
        $this->cacheMock->shouldReceive('forever')
             ->byDefault()
             ->andReturn(true);
        $this->cacheMock->shouldReceive('forget')
             ->byDefault()
             ->andReturn(true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_help_command_returns_formatted_help_information()
    {
        // Create a mock command
        $mockCommand = new class implements CommandInterface
        {
            public function getName(): string
            {return 'test';}
            public function getDescription(): string
            {return 'Test command';}
            public function execute(Terminal $terminal, array $args = []): array
            {return ['Test executed'];}
            public function getUsage(): string
            {return 'test [options]';}
            public function isHidden(): bool
            {return false;}
            public function getAliases(): array
            {return [];}
        };

        // Create a mock registry with the test command
        $registry = new CommandRegistry();
        $registry->register($mockCommand);

        $command = new HelpCommand($registry);
        $terminal = new Terminal();
        $result = $command->execute($terminal);

        // Check that the result contains the help information
        $this->assertCount(6, $result); // Header + table + empty line + help info
        $this->assertStringContainsString('calkeOS Terminal Help', $result[0]);
        $this->assertStringContainsString('test', $result[1]);
        $this->assertStringContainsString('Test command', $result[1]);
        $this->assertStringContainsString('help &lt;command&gt;', $result[3]);
    }

    public function test_help_command_with_specific_command_returns_detailed_help()
    {
        // Create a mock command
        $mockCommand = new class implements CommandInterface
        {
            public function getName(): string
            {return 'test';}
            public function getDescription(): string
            {return 'Test command';}
            public function execute(Terminal $terminal, array $args = []): array
            {return ['Test executed'];}
            public function getUsage(): string
            {return 'test [options]';}
            public function isHidden(): bool
            {return false;}
            public function getAliases(): array
            {return [];}
        };

        // Create a mock registry with the test command
        $registry = new CommandRegistry();
        $registry->register($mockCommand);

        $command = new HelpCommand($registry);
        $terminal = new Terminal();
        $result = $command->execute($terminal, ['test']);

        // Check that the result contains the detailed help information
        $this->assertCount(2, $result); // Header + box
        $this->assertStringContainsString('Command: test', $result[0]);
        $this->assertStringContainsString('Description:', $result[1]);
        $this->assertStringContainsString('Test command', $result[1]);
        $this->assertStringContainsString('Usage:', $result[1]);
        $this->assertStringContainsString('test [options]', $result[1]);
    }

    public function test_help_command_with_non_existent_command_returns_error()
    {
        $registry = new CommandRegistry();
        $command = new HelpCommand($registry);
        $terminal = new Terminal();
        $result = $command->execute($terminal, ['nonexistent']);

        // Check that the result contains the error message
        $this->assertCount(1, $result);
        $this->assertStringContainsString('Command not found: nonexistent', $result[0]);
    }
}
