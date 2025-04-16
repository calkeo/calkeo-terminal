<?php

use App\Commands\WhoamiCommand;
use Illuminate\Support\Facades\Session;

test('whoami command returns username from session', function () {
    // Set up session
    Session::shouldReceive('get')
        ->with('terminal_username', 'guest')
        ->once()
        ->andReturn('testuser');

    $command = new WhoamiCommand();
    $result = $command->execute();

    // Check that the result contains the username
    expect($result[0])->toBe('testuser');

    // Check that the result contains user information
    expect($result)->toHaveCount(6); // Username + empty line + header + 3 info lines
    expect($result[2])->toContain('User Information:');
    expect($result[3])->toContain('uid=');
    expect($result[3])->toContain('testuser');
    expect($result[4])->toContain('home=');
    expect($result[4])->toContain('/home/testuser');
    expect($result[5])->toContain('host=');
    expect($result[5])->toContain('calkeos');
});

test('whoami command uses guest as fallback', function () {
    // Set up session to return null
    Session::shouldReceive('get')
        ->with('terminal_username', 'guest')
        ->once()
        ->andReturn('guest');

    $command = new WhoamiCommand();
    $result = $command->execute();

    // Check that the result contains the fallback username
    expect($result[0])->toBe('guest');

    // Check that the result contains user information
    expect($result[4])->toContain('/home/guest');
});
