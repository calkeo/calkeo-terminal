<?php

use App\Commands\EchoCommand;

test('echo command displays usage when no arguments provided', function () {
    $command = new EchoCommand();
    $output = $command->execute();

    expect($output)->toHaveCount(2);
    expect($output[0])->toContain('Usage: echo <text>');
    expect($output[0])->toContain('text-yellow-400'); // warning style
    expect($output[1])->toContain('Example: echo Hello, World!');
    expect($output[1])->toContain('text-cyan-400'); // info style
});

test('echo command displays text when arguments provided', function () {
    $command = new EchoCommand();
    $output = $command->execute(['Hello', 'World']);

    expect($output)->toHaveCount(1);
    expect($output[0])->toContain('Hello World');
    expect($output[0])->toContain('text-green-400'); // default style
});

test('echo command handles quoted strings', function () {
    $command = new EchoCommand();
    $output = $command->execute(['"Hello World"']);

    expect($output)->toHaveCount(1);
    expect($output[0])->toContain('Hello World');
});

test('echo command handles single quoted strings', function () {
    $command = new EchoCommand();
    $output = $command->execute(["'Hello World'"]);

    expect($output)->toHaveCount(1);
    expect($output[0])->toContain('Hello World');
    expect($output[0])->not->toContain("'");
});

test('echo command handles multiple arguments with spaces', function () {
    $command = new EchoCommand();
    $output = $command->execute(['Hello', 'Beautiful', 'World']);

    expect($output)->toHaveCount(1);
    expect($output[0])->toContain('Hello Beautiful World');
});