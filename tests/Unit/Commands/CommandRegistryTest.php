<?php

use App\Commands\CommandInterface;
use App\Commands\CommandRegistry;

class MockCommand implements CommandInterface
{
    protected $name;
    protected $description;

    public function __construct(string $name, string $description)
    {
        $this->name = $name;
        $this->description = $description;
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
}

test('register adds command to registry', function () {
    $registry = new CommandRegistry();
    $command = new MockCommand('test', 'Test command');

    $registry->register($command);

    expect($registry->has('test'))->toBeTrue();
    expect($registry->get('test'))->toBe($command);
});

test('get returns null for non-existent command', function () {
    $registry = new CommandRegistry();

    expect($registry->get('nonexistent'))->toBeNull();
});

test('has returns false for non-existent command', function () {
    $registry = new CommandRegistry();

    expect($registry->has('nonexistent'))->toBeFalse();
});

test('all returns collection of all registered commands', function () {
    $registry = new CommandRegistry();
    $command1 = new MockCommand('test1', 'Test command 1');
    $command2 = new MockCommand('test2', 'Test command 2');

    $registry->register($command1);
    $registry->register($command2);

    $all = $registry->all();

    expect($all->count())->toBe(2);
    expect($all->get('test1'))->toBe($command1);
    expect($all->get('test2'))->toBe($command2);
});

test('getHelp returns formatted help information', function () {
    $registry = new CommandRegistry();
    $command1 = new MockCommand('test1', 'Test command 1');
    $command2 = new MockCommand('test2', 'Test command 2');

    $registry->register($command1);
    $registry->register($command2);

    $help = $registry->getHelp();

    expect(count($help))->toBe(2);
    expect($help[0])->toContain('test1');
    expect($help[0])->toContain('Test command 1');
    expect($help[1])->toContain('test2');
    expect($help[1])->toContain('Test command 2');
});
