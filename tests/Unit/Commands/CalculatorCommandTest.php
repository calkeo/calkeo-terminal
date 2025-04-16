<?php

use App\Commands\CalculatorCommand;

test('calculator command shows usage when no arguments provided', function () {
    $command = new CalculatorCommand();
    $output = $command->execute();

    expect($output)->toHaveCount(3);
    expect($output[0])->toContain('Usage: calc <expression>');
    expect($output[1])->toContain('Example: calc 2 + 2');
    expect($output[2])->toContain('Supported operations: +, -, *, /, %');
});

test('calculator performs basic arithmetic operations', function () {
    $command = new CalculatorCommand();

    // Addition
    $output = $command->execute(['2', '+', '2']);
    expect($output[0])->toContain('<span class="text-purple-400">2+2</span> = <span class="text-pink-400">4</span>');

    // Subtraction
    $output = $command->execute(['5', '-', '3']);
    expect($output[0])->toContain('<span class="text-purple-400">5-3</span> = <span class="text-pink-400">2</span>');

    // Multiplication
    $output = $command->execute(['4', '*', '3']);
    expect($output[0])->toContain('<span class="text-purple-400">4*3</span> = <span class="text-pink-400">12</span>');

    // Division
    $output = $command->execute(['10', '/', '2']);
    expect($output[0])->toContain('<span class="text-purple-400">10/2</span> = <span class="text-pink-400">5</span>');

    // Modulo
    $output = $command->execute(['7', '%', '3']);
    expect($output[0])->toContain('<span class="text-purple-400">7%3</span> = <span class="text-pink-400">1</span>');
});

test('calculator handles decimal numbers', function () {
    $command = new CalculatorCommand();

    $output = $command->execute(['3.14', '*', '2']);
    expect($output[0])->toContain('<span class="text-purple-400">3.14*2</span> = <span class="text-pink-400">6.28</span>');

    $output = $command->execute(['10', '/', '3']);
    expect($output[0])->toContain('<span class="text-purple-400">10/3</span> = <span class="text-pink-400">3.333333</span>');
});

test('calculator handles expressions with parentheses', function () {
    $command = new CalculatorCommand();

    $output = $command->execute(['(', '2', '+', '3', ')', '*', '4']);
    expect($output[0])->toContain('<span class="text-purple-400">(2+3)*4</span> = <span class="text-pink-400">20</span>');

    $output = $command->execute(['2', '*', '(', '3', '+', '4', ')']);
    expect($output[0])->toContain('<span class="text-purple-400">2*(3+4)</span> = <span class="text-pink-400">14</span>');
});

test('calculator handles complex expressions', function () {
    $command = new CalculatorCommand();

    $output = $command->execute(['2', '+', '3', '*', '4']);
    expect($output[0])->toContain('<span class="text-purple-400">2+3*4</span> = <span class="text-pink-400">14</span>');

    $output = $command->execute(['(', '2', '+', '3', ')', '*', '(', '4', '-', '1', ')']);
    expect($output[0])->toContain('<span class="text-purple-400">(2+3)*(4-1)</span> = <span class="text-pink-400">15</span>');
});

test('calculator handles error cases', function () {
    $command = new CalculatorCommand();

    // Division by zero
    $output = $command->execute(['1', '/', '0']);
    expect($output[0])->toContain('<span class="text-red-400">Error: Division by zero or invalid operation</span>');

    // Invalid characters
    $output = $command->execute(['2', '+', 'a']);
    expect($output[0])->toContain('<span class="text-red-400">Error: Invalid characters in expression</span>');

    // Mismatched parentheses
    $output = $command->execute(['(', '2', '+', '3']);
    expect($output[0])->toContain('<span class="text-red-400">Error: Mismatched parentheses</span>');

    // Invalid expression
    $output = $command->execute(['2', '+', '+', '3']);
    expect($output[0])->toContain('<span class="text-red-400">Error: Invalid expression</span>');
});
