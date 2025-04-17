<?php

use App\Commands\SshCommand;
use Tests\TestCase;

class SshCommandTest extends TestCase
{
    public function test_ssh_command_displays_usage_when_no_host_provided()
    {
        $command = new SshCommand();
        $output = $command->execute();

        // Check that the output contains usage information
        $this->assertCount(2, $output);
        $this->assertStringContainsString('Usage: ssh <hostname>', $output[0]);
        $this->assertStringContainsString('text-yellow-400', $output[0]); // warning style
        $this->assertStringContainsString('Example: ssh user@example.com', $output[1]);
        $this->assertStringContainsString('text-cyan-400', $output[1]); // info style
    }

    public function test_ssh_command_returns_delayed_output_when_host_provided()
    {
        $command = new SshCommand();
        $output = $command->execute(['example.com']);

        // Check that the output contains delayed output
        $this->assertGreaterThan(1, count($output));

        // First line should be immediate (not delayed)
        $this->assertStringContainsString('Connecting to example.com', $output[0]);
        $this->assertStringContainsString('text-cyan-400', $output[0]); // info style

        // Check that there are delayed outputs
        $hasDelayedOutput = false;
        foreach ($output as $line) {
            if (is_array($line) && isset($line['type']) && $line['type'] === 'delayed') {
                $hasDelayedOutput = true;
                $this->assertArrayHasKey('delay', $line);
                $this->assertArrayHasKey('content', $line);
                $this->assertIsInt($line['delay']);
                $this->assertGreaterThan(0, $line['delay']);
            }
        }

        $this->assertTrue($hasDelayedOutput, 'No delayed output found in SSH command response');
    }

    public function test_ssh_command_contains_expected_connection_messages()
    {
        $command = new SshCommand();
        $output = $command->execute(['example.com']);

        // Check for expected messages in the output
        $expectedMessages = [
            'Connecting to example.com',
            "The authenticity of host 'example.com' can't be established",
            'ECDSA key fingerprint is SHA256:',
            'Are you sure you want to continue connecting',
            'yes',
            "Warning: Permanently added 'example.com'",
            'Password:',
            '********',
            'Welcome to Ubuntu',
            'Last login:',
        ];

        $outputText = '';
        foreach ($output as $line) {
            if (is_array($line) && isset($line['content'])) {
                $outputText .= $line['content'] . ' ';
            } else {
                $outputText .= $line . ' ';
            }
        }

        foreach ($expectedMessages as $message) {
            $this->assertStringContainsString($message, $outputText, "Expected message '$message' not found in SSH output");
        }
    }
}
