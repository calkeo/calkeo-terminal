<?php

use App\Commands\AbstractCommand;

class TestCommand extends AbstractCommand
{
    protected $name = 'test';
    protected $description = 'Test command';

    public function execute(array $args = []): array
    {
        return ['Test command executed'];
    }
}

test('formatOutput applies correct styling', function () {
    $command = new TestCommand();

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('formatOutput');
    $method->setAccessible(true);

    // Test default style
    $result = $method->invoke($command, 'Test text');
    expect($result)->toContain('text-green-400');
    expect($result)->toContain('Test text');

    // Test error style
    $result = $method->invoke($command, 'Error message', 'error');
    expect($result)->toContain('text-red-400');
    expect($result)->toContain('Error message');

    // Test info style
    $result = $method->invoke($command, 'Info message', 'info');
    expect($result)->toContain('text-cyan-400');
    expect($result)->toContain('Info message');

    // Test warning style
    $result = $method->invoke($command, 'Warning message', 'warning');
    expect($result)->toContain('text-yellow-400');
    expect($result)->toContain('Warning message');

    // Test command style
    $result = $method->invoke($command, 'Command name', 'command');
    expect($result)->toContain('text-purple-400');
    expect($result)->toContain('Command name');

    // Test path style
    $result = $method->invoke($command, '/path/to/file', 'path');
    expect($result)->toContain('text-blue-400');
    expect($result)->toContain('/path/to/file');

    // Test value style
    $result = $method->invoke($command, 'Some value', 'value');
    expect($result)->toContain('text-pink-400');
    expect($result)->toContain('Some value');

    // Test header style
    $result = $method->invoke($command, 'Header text', 'header');
    expect($result)->toContain('text-cyan-400');
    expect($result)->toContain('font-bold');
    expect($result)->toContain('Header text');

    // Test subheader style
    $result = $method->invoke($command, 'Subheader text', 'subheader');
    expect($result)->toContain('text-yellow-400');
    expect($result)->toContain('font-semibold');
    expect($result)->toContain('Subheader text');
});

test('createStyledBox creates properly formatted box', function () {
    $command = new TestCommand();

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('createStyledBox');
    $method->setAccessible(true);

    $lines = ['Line 1', 'Line 2', 'Line 3'];
    $title = 'Test Box';

    $result = $method->invoke($command, $lines, $title);

    // Check for box container
    expect($result)->toContain('font-[\'JetBrains_Mono\']');
    expect($result)->toContain('border border-gray-700');

    // Check for title
    expect($result)->toContain('bg-gray-800');
    expect($result)->toContain('$');
    expect($result)->toContain('Test Box');

    // Check for content
    expect($result)->toContain('Line 1');
    expect($result)->toContain('Line 2');
    expect($result)->toContain('Line 3');
});

test('createStyledTable creates properly formatted table', function () {
    $command = new TestCommand();

    $reflection = new ReflectionClass($command);
    $method = $reflection->getMethod('createStyledTable');
    $method->setAccessible(true);

    $headers = ['Header 1', 'Header 2'];
    $rows = [
        ['Row 1 Col 1', 'Row 1 Col 2'],
        ['Row 2 Col 1', 'Row 2 Col 2'],
    ];

    $result = $method->invoke($command, $headers, $rows);

    // Check for table container
    expect($result)->toContain('font-[\'JetBrains_Mono\']');
    expect($result)->toContain('border border-gray-700');

    // Check for headers
    expect($result)->toContain('Header 1');
    expect($result)->toContain('Header 2');

    // Check for rows
    expect($result)->toContain('Row 1 Col 1');
    expect($result)->toContain('Row 1 Col 2');
    expect($result)->toContain('Row 2 Col 1');
    expect($result)->toContain('Row 2 Col 2');
});
