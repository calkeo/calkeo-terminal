<?php

namespace Tests\Unit\Commands;

use App\Commands\CommandInterface;
use App\Commands\CommandRegistry;
use App\Commands\SshCommand;
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

    public function execute(array $args = []): array
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

    protected function setUp(): void
    {
        parent::setUp();
        $this->registry = new CommandRegistry();
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
        // Check that the SSH command is registered
        $this->assertTrue($this->registry->has('ssh'));
        $this->assertInstanceOf(SshCommand::class, $this->registry->get('ssh'));

        // Check that the SSH command alias is registered
        $this->assertTrue($this->registry->has('ssh-connect'));
        $this->assertInstanceOf(SshCommand::class, $this->registry->get('ssh-connect'));
    }

    public function test_command_registry_returns_all_commands()
    {
        $commands = $this->registry->all();

        // Check that the commands collection is not empty
        $this->assertNotEmpty($commands);

        // Check that it contains the SSH command
        $this->assertTrue($commands->has('ssh'));
        $this->assertInstanceOf(SshCommand::class, $commands->get('ssh'));
    }

    public function test_command_registry_returns_help_information()
    {
        $help = $this->registry->getHelp();

        // Check that the help information is not empty
        $this->assertNotEmpty($help);

        // Check that it contains the SSH command
        $hasSshCommand = false;
        foreach ($help as $line) {
            if (strpos($line, 'ssh') === 0) {
                $hasSshCommand = true;
                $this->assertStringContainsString('Connect to a remote server via SSH', $line);
                break;
            }
        }

        $this->assertTrue($hasSshCommand, 'SSH command not found in help information');
    }
}
