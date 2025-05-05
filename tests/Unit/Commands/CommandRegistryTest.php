<?php

namespace Tests\Unit\Commands;

use App\Commands\CommandInterface;
use App\Commands\CommandRegistry;
use App\Commands\SshCommand;
use App\Livewire\Terminal;
use App\Providers\CommandServiceProvider;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Mockery;
use Tests\TestCase;

class MockCommand implements CommandInterface
{
    protected $name;
    protected $description;
    protected $aliases = [];

    public function __construct(string $name, string $description, array $aliases = [])
    {
        $this->name = $name;
        $this->description = $description;
        $this->aliases = $aliases;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function execute(Terminal $terminal, array $args = []): array
    {
        return ['Mock command executed'];
    }

    public function getUsage(): string
    {
        return $this->name;
    }

    public function getAliases(): array
    {
        return $this->aliases;
    }
}

class CommandRegistryTest extends TestCase
{
    protected $registry;
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

        // Register the service provider
        $this->app->register(CommandServiceProvider::class);

        // Get the registry instance
        $this->registry = $this->app->make(CommandRegistry::class);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_register_adds_command_to_registry()
    {
        $command = new MockCommand('test', 'Test command');
        $this->registry->register($command);

        $this->assertTrue($this->registry->has('test'));
        $this->assertSame($command, $this->registry->get('test'));
    }

    public function test_get_returns_null_for_non_existent_command()
    {
        $this->assertNull($this->registry->get('nonexistent'));
    }

    public function test_has_returns_false_for_non_existent_command()
    {
        $this->assertFalse($this->registry->has('nonexistent'));
    }

    public function test_all_returns_collection_of_all_registered_commands()
    {
        $command1 = new MockCommand('test1', 'Test command 1');
        $command2 = new MockCommand('test2', 'Test command 2');

        $this->registry->register($command1);
        $this->registry->register($command2);

        $all = $this->registry->all();

        // Get the count of built-in commands
        $builtInCommands = $all->count() - 2; // Subtract our 2 test commands

        $this->assertTrue($all->has('test1'));
        $this->assertTrue($all->has('test2'));
        $this->assertSame($command1, $all->get('test1'));
        $this->assertSame($command2, $all->get('test2'));
    }

    public function test_getHelp_returns_formatted_help_information()
    {
        $command1 = new MockCommand('test1', 'Test command 1');
        $command2 = new MockCommand('test2', 'Test command 2');

        $this->registry->register($command1);
        $this->registry->register($command2);

        $help = $this->registry->getHelp();

        // Find our test commands in the help output
        $test1Found = false;
        $test2Found = false;

        foreach ($help as $line) {
            if (strpos($line, 'test1') === 0) {
                $test1Found = true;
                $this->assertStringContainsString('Test command 1', $line);
            }
            if (strpos($line, 'test2') === 0) {
                $test2Found = true;
                $this->assertStringContainsString('Test command 2', $line);
            }
        }

        $this->assertTrue($test1Found, 'test1 command not found in help output');
        $this->assertTrue($test2Found, 'test2 command not found in help output');
    }

    public function test_register_adds_command_aliases_to_registry()
    {
        $command = new MockCommand('test', 'Test command', ['alias1', 'alias2']);
        $this->registry->register($command);

        $this->assertTrue($this->registry->has('alias1'));
        $this->assertTrue($this->registry->has('alias2'));
        $this->assertSame($command, $this->registry->get('alias1'));
        $this->assertSame($command, $this->registry->get('alias2'));
    }

    public function test_get_returns_command_when_using_alias()
    {
        $command = new MockCommand('test', 'Test command', ['alias1']);
        $this->registry->register($command);

        $this->assertSame($command, $this->registry->get('alias1'));
    }

    public function test_has_returns_true_for_alias()
    {
        $command = new MockCommand('test', 'Test command', ['alias1']);
        $this->registry->register($command);

        $this->assertTrue($this->registry->has('alias1'));
    }

    public function test_getHelp_includes_aliases_in_help_information()
    {
        $command = new MockCommand('test', 'Test command', ['alias1', 'alias2']);
        $this->registry->register($command);

        $help = $this->registry->getHelp();

        $testCommandFound = false;
        foreach ($help as $line) {
            if (strpos($line, 'test') === 0) {
                $testCommandFound = true;
                $this->assertStringContainsString('Test command', $line);
                $this->assertStringContainsString('(aliases: alias1, alias2)', $line);
                break;
            }
        }

        $this->assertTrue($testCommandFound, 'Test command not found in help output');
    }

    public function test_command_registry_registers_ssh_command()
    {
        // Register the SSH command
        $sshCommand = new SshCommand();
        $this->registry->register($sshCommand);

        // Check that the SSH command is registered
        $this->assertTrue($this->registry->has('ssh'));
        $this->assertInstanceOf(SshCommand::class, $this->registry->get('ssh'));

        // Check that the SSH command alias is registered
        $this->assertTrue($this->registry->has('ssh-connect'));
        $this->assertInstanceOf(SshCommand::class, $this->registry->get('ssh-connect'));
    }

    public function test_command_registry_returns_all_commands()
    {
        // Register a test command
        $command = new MockCommand('test', 'Test command');
        $this->registry->register($command);

        $commands = $this->registry->all();

        // Check that the commands collection is not empty
        $this->assertNotEmpty($commands);

        // Check that it contains our test command
        $this->assertTrue($commands->has('test'));
        $this->assertInstanceOf(MockCommand::class, $commands->get('test'));
    }

    public function test_command_registry_returns_help_information()
    {
        // Register a test command
        $command = new MockCommand('test', 'Test command');
        $this->registry->register($command);

        $help = $this->registry->getHelp();

        // Check that the help information is not empty
        $this->assertNotEmpty($help);

        // Check that it contains our test command
        $hasTestCommand = false;
        foreach ($help as $line) {
            if (strpos($line, 'test') === 0) {
                $hasTestCommand = true;
                $this->assertStringContainsString('Test command', $line);
                break;
            }
        }

        $this->assertTrue($hasTestCommand, 'Test command not found in help information');
    }

    public function test_command_registry_loads_from_cache()
    {
        // Create a command
        $command = new MockCommand('test', 'Test command');
        $commands = new Collection(['test' => $command]);

        // Set up cache expectations
        $this->cacheMock->shouldReceive('get')
             ->with('command_registry', Mockery::type(Collection::class))
             ->andReturn($commands);
        $this->cacheMock->shouldReceive('get')
             ->with('command_aliases', Mockery::type(Collection::class))
             ->andReturn(new Collection());

        // Create a new registry instance
        $newRegistry = new CommandRegistry();

        // Verify the command is loaded from cache
        $this->assertTrue($newRegistry->has('test'));
        $this->assertEquals('test', $newRegistry->get('test')->getName());
    }

    public function test_command_registry_clears_cache()
    {
        // Set up cache expectations for clearing
        $this->cacheMock->shouldReceive('forget')
             ->with('command_registry')
             ->once()
             ->andReturn(true);
        $this->cacheMock->shouldReceive('forget')
             ->with('command_aliases')
             ->once()
             ->andReturn(true);

        // Clear the cache
        $this->registry->clearCache();

        // Create a new registry instance
        $newRegistry = new CommandRegistry();

        // Verify the registry is empty
        $this->assertEmpty($newRegistry->all());
    }

    public function test_static_clear_cache_method_works()
    {
        // Set up cache expectations for clearing
        $this->cacheMock->shouldReceive('forget')
             ->with('command_registry')
             ->once()
             ->andReturn(true);
        $this->cacheMock->shouldReceive('forget')
             ->with('command_aliases')
             ->once()
             ->andReturn(true);

        // Clear the cache using static method
        CommandRegistry::staticClearCache();

        // Create a new registry instance
        $newRegistry = new CommandRegistry();

        // Verify the registry is empty
        $this->assertEmpty($newRegistry->all());
    }

    public function test_command_registry_saves_to_cache_on_register()
    {
        // Create a command
        $command = new MockCommand('test', 'Test command');

        // Set up cache expectations for saving
        $this->cacheMock->shouldReceive('forever')
            ->with('command_registry', Mockery::on(function ($value) {
                return $value->has('test');
            }))
            ->once();
        $this->cacheMock->shouldReceive('forever')
             ->with('command_aliases', Mockery::type(Collection::class))
             ->once();

        // Register the command
        $this->registry->register($command);

        // Set up cache expectations for loading
        $this->cacheMock->shouldReceive('get')
             ->with('command_registry', Mockery::type(Collection::class))
             ->andReturn(new Collection(['test' => $command]));

        // Create a new registry instance
        $newRegistry = new CommandRegistry();

        // Verify the command is in the new registry
        $this->assertTrue($newRegistry->has('test'));
        $this->assertEquals('test', $newRegistry->get('test')->getName());
    }

    public function test_command_registry_handles_empty_cache()
    {
        // Set up cache expectations for empty cache
        $this->cacheMock->shouldReceive('get')
             ->with('command_registry', Mockery::type(Collection::class))
             ->andReturn(new Collection());
        $this->cacheMock->shouldReceive('get')
             ->with('command_aliases', Mockery::type(Collection::class))
             ->andReturn(new Collection());

        // Create a new registry instance
        $newRegistry = new CommandRegistry();

        // Verify the registry is empty
        $this->assertEmpty($newRegistry->all());
    }
}
