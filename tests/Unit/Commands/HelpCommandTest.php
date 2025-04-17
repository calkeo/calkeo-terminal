<?php

use App\Commands\CommandInterface;
use App\Commands\CommandRegistry;
use App\Commands\HelpCommand;

test('help command returns formatted help information', function () {
    // Create a mock command
    $mockCommand = new class implements CommandInterface
    {
        public function getName(): string
        {return 'test';}
        public function getDescription(): string
        {return 'Test command';}
        public function execute(array $args = []): array
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
    $result = $command->execute();

    // Check that the result contains the help information
    expect($result)->toHaveCount(4); // Header + table + empty line + help info
    expect($result[0])->toContain('calkeOS Terminal Help');
    expect($result[1])->toContain('test');
    expect($result[1])->toContain('Test command');
    expect($result[3])->toContain('help &lt;command&gt;');
});

test('help command with specific command returns detailed help', function () {
    // Create a mock command
    $mockCommand = new class implements CommandInterface
    {
        public function getName(): string
        {return 'test';}
        public function getDescription(): string
        {return 'Test command';}
        public function execute(array $args = []): array
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
    $result = $command->execute(['test']);

    // Check that the result contains the detailed help information
    expect($result)->toHaveCount(2); // Header + box
    expect($result[0])->toContain('Command: test');
    expect($result[1])->toContain('Description:');
    expect($result[1])->toContain('Test command');
    expect($result[1])->toContain('Usage:');
    expect($result[1])->toContain('test [options]');
});

test('help command with non-existent command returns error', function () {
    $registry = new CommandRegistry();
    $command = new HelpCommand($registry);
    $result = $command->execute(['nonexistent']);

    // Check that the result contains the error message
    expect($result)->toHaveCount(1);
    expect($result[0])->toContain('Command not found: nonexistent');
});
