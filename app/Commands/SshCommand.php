<?php

namespace App\Commands;

class SshCommand extends AbstractCommand
{
    protected $name = 'ssh';
    protected $description = 'Connect to a remote server via SSH';
    protected $aliases = ['ssh-connect'];

    public function execute(array $args = []): array
    {
        if (empty($args)) {
            return [
                $this->formatOutput("Usage: ssh <hostname>", 'warning'),
                $this->formatOutput("Example: ssh user@example.com", 'info'),
            ];
        }

        $host = $args[0];
        $output = [];
        $sha_fingerprint = \Illuminate\Support\Str::random(64);

        // Initial connection message
        $output[] = [
            'type' => 'delayed',
            'delay' => 0,
            'content' => $this->formatOutput("Connecting to {$host}...", 'info'),
        ];

        // Simulate SSH connection steps with delays
        $output[] = [
            'type' => 'delayed',
            'delay' => 2000,
            'content' => $this->formatOutput("The authenticity of host '{$host}' can't be established.", 'warning'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 1000,
            'content' => $this->formatOutput("ECDSA key fingerprint is SHA256:{$sha_fingerprint}", 'value'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 0,
            'content' => $this->formatOutput("Are you sure you want to continue connecting (yes/no/[fingerprint])? ", 'info'),
        ];

        // Simulate user accepting the connection
        $output[] = [
            'type' => 'delayed',
            'delay' => 1000,
            'content' => $this->formatOutput("yes", 'command'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 250,
            'content' => $this->formatOutput("Warning: Permanently added '{$host}' (ECDSA) to the list of known hosts.", 'info'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 1500,
            'content' => $this->formatOutput("Password: ", 'info'),
        ];

        // Simulate password entry
        $output[] = [
            'type' => 'delayed',
            'delay' => 1000,
            'content' => $this->formatOutput("********", 'command'),
        ];

        // Connection success message
        $output[] = [
            'type' => 'delayed',
            'delay' => 1000,
            'content' => $this->formatOutput("Welcome to Ubuntu 22.04.3 LTS (GNU/Linux 5.15.0-91-generic x86_64)", 'success'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 0,
            'content' => $this->formatOutput("Last login: " . date('D M j H:i:s T Y'), 'info'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 2000,
            'content' => $this->formatOutput("-- INTRUSION DETECTED --", 'error'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 2000,
            'content' => $this->formatOutput("SELF DESTRUCT SEQUENCE INITIATED", 'error'),
        ];

        // Add countdown from 10 to 1
        for ($i = 10; $i >= 1; $i--) {
            $output[] = [
                'type' => 'delayed',
                'delay' => 1000,
                'content' => $this->formatOutput($i . "...", 'error'),
            ];
        }

        $output[] = [
            'type' => 'delayed',
            'delay' => 2000,
            'content' => $this->formatOutput("Well this is awkward...", 'normal'),
        ];

        // Final explosion message
        $output[] = [
            'type' => 'delayed',
            'delay' => 2000,
            'content' => $this->formatOutput("💥 BOOM! 💥", 'error'),
        ];

        return $output;
    }
}