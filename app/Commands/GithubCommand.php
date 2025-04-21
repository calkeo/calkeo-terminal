<?php

namespace App\Commands;

use App\Livewire\Terminal;

class GithubCommand extends AbstractCommand
{
    protected $name = 'git';
    protected $description = 'Displays git info';
    protected $aliases = ['github'];

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        $output = [];

        // GitHub username - you can replace this with your actual GitHub username
        $githubUsername = 'calkeo';
        $githubUrl = "https://github.com/{$githubUsername}";

        // Git style output
        $output[] = $this->formatOutput("remote.origin.url", 'command') . " " . $this->formatOutput($githubUrl, 'value');
        $output[] = $this->formatOutput("remote.origin.fetch", 'command') . " " . $this->formatOutput("+refs/heads/*:refs/remotes/origin/*", 'value');
        $output[] = $this->formatOutput("remote.origin.pushurl", 'command') . " " . $this->formatOutput($githubUrl, 'value');
        $output[] = $this->formatOutput("remote.origin.push", 'command') . " " . $this->formatOutput("refs/heads/main:refs/heads/main", 'value');

        // Add a clickable link
        $output[] = '';
        $output[] = $this->formatOutput("Visit my GitHub profile:", 'header');
        $output[] = "<a href=\"{$githubUrl}\" target=\"_blank\" class=\"text-blue-400 hover:underline\">{$githubUrl}</a>";

        return $output;
    }
}
