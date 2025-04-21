<?php

namespace Tests\Unit\Commands;

use App\Commands\WhoamiCommand;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class WhoamiCommandTest extends TestCase
{
    protected $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new WhoamiCommand();
    }

    public function test_whoami_command_returns_username_from_session()
    {
        // Mock the session
        Session::shouldReceive('get')
            ->with('terminal_username', 'guest')
            ->once()
            ->andReturn('testuser');

        $output = $this->command->execute();

        // Check that we have all expected lines
        $this->assertCount(6, $output);

        // Check username line
        $this->assertEquals('testuser', $output[0]);

        // Check empty line
        $this->assertEquals('', $output[1]);

        // Check header
        $this->assertStringContainsString('User Information:', $output[2]);

        // Check user details
        $this->assertStringContainsString('uid=', $output[3]);
        $this->assertStringContainsString('testuser', $output[3]);

        // Check home directory
        $this->assertStringContainsString('home=', $output[4]);
        $this->assertStringContainsString('/home/testuser', $output[4]);

        // Check hostname
        $this->assertStringContainsString('host=', $output[5]);
        $this->assertStringContainsString('calkeo.dev', $output[5]);
    }

    public function test_whoami_command_uses_guest_as_fallback()
    {
        // Mock the session to return guest
        Session::shouldReceive('get')
            ->with('terminal_username', 'guest')
            ->once()
            ->andReturn('guest');

        $output = $this->command->execute();

        // Check that we have all expected lines
        $this->assertCount(6, $output);

        // Check username line
        $this->assertEquals('guest', $output[0]);

        // Check empty line
        $this->assertEquals('', $output[1]);

        // Check header
        $this->assertStringContainsString('User Information:', $output[2]);

        // Check user details
        $this->assertStringContainsString('uid=', $output[3]);
        $this->assertStringContainsString('guest', $output[3]);

        // Check home directory
        $this->assertStringContainsString('home=', $output[4]);
        $this->assertStringContainsString('/home/guest', $output[4]);

        // Check hostname
        $this->assertStringContainsString('host=', $output[5]);
        $this->assertStringContainsString('calkeo.dev', $output[5]);
    }
}