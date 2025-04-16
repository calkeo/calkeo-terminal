<?php

use App\Commands\CommandParser;

test('parse splits command and arguments correctly', function () {
    $parser = new CommandParser();

    // Test simple command
    $result = $parser->parse('help');
    expect($result['command'])->toBe('help');
    expect($result['args'])->toBe([]);

    // Test command with arguments
    $result = $parser->parse('help whoami');
    expect($result['command'])->toBe('help');
    expect($result['args'])->toBe(['whoami']);

    // Test command with multiple arguments
    $result = $parser->parse('sudo rm -rf /');
    expect($result['command'])->toBe('sudo');
    expect($result['args'])->toBe(['rm', '-rf', '/']);

    // Test empty input
    $result = $parser->parse('');
    expect($result['command'])->toBe('');
    expect($result['args'])->toBe([]);

    // Test whitespace
    $result = $parser->parse('  help  whoami  ');
    expect($result['command'])->toBe('help');
    expect($result['args'])->toBe(['whoami']);
});

test('parse handles quoted strings correctly', function () {
    $parser = new CommandParser();

    // Test single quoted string
    $result = $parser->parse('echo \'Hello World\'');
    expect($result['command'])->toBe('echo');
    expect($result['args'])->toBe(['Hello World']);

    // Test double quoted string
    $result = $parser->parse('echo "Hello World"');
    expect($result['command'])->toBe('echo');
    expect($result['args'])->toBe(['Hello World']);

    // Test mixed quotes
    $result = $parser->parse('echo "Hello \'World\'"');
    expect($result['command'])->toBe('echo');
    expect($result['args'])->toBe(['Hello \'World\'']);

    // Test escaped quotes
    $result = $parser->parse('echo \"Hello World\"');
    expect($result['command'])->toBe('echo');
    expect($result['args'])->toBe(['\"Hello', 'World\"']);
});
