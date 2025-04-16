<?php

namespace App\Livewire;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use Livewire\Component;

class Terminal extends Component
{
    public $command = '';
    public $output = [];
    public $commandHistory = [];
    public $historyIndex = -1;
    public $suggestions = [];
    public $showSuggestions = false;
    public $username = '';

    protected $commandRegistry;
    protected $commandParser;

    public function boot(CommandRegistry $registry, CommandParser $parser)
    {
        $this->commandRegistry = $registry;
        $this->commandParser = $parser;
    }

    public function mount()
    {
        // Check if user is logged in
        if (!session('terminal_logged_in')) {
            return $this->redirect('/');
        }

        // Get username from session
        $this->username = session('terminal_username', 'guest');

        $this->output[] = $this->formatWelcomeMessage();
    }

    public function executeCommand()
    {
        if (empty($this->command)) {
            return;
        }

        // Add command to output
        $this->output[] = "\$ " . $this->command;

        // Add command to history
        $this->commandHistory[] = $this->command;
        $this->historyIndex = count($this->commandHistory);

        // Parse the command
        $parsed = $this->commandParser->parse($this->command);
        $commandName = $parsed['command'];
        $args = $parsed['args'];

        // Clear the command input
        $this->command = '';

        // Handle empty command
        if (empty($commandName)) {
            return;
        }

        // Find and execute the command
        $command = $this->commandRegistry->get($commandName);

        if ($command) {
            $result = $command->execute($args);

            // Handle special clear command
            if ($result === ['__CLEAR__']) {
                $this->output = [$this->formatWelcomeMessage()];
                return;
            }

            // Handle logout command
            if (in_array('__LOGOUT__', $result)) {
                $this->output = array_merge($this->output, array_diff($result, ['__LOGOUT__']));
                return $this->redirect('/');
            }

            $this->output = array_merge($this->output, $result);
        } else {
            $this->output[] = "Command not found: {$commandName}";
            $this->output[] = "Type 'help' to see available commands.";
        }
    }

    public function getPreviousCommand()
    {
        if ($this->historyIndex > 0) {
            $this->historyIndex--;
            $this->command = $this->commandHistory[$this->historyIndex];
        }
    }

    public function getNextCommand()
    {
        if ($this->historyIndex < count($this->commandHistory) - 1) {
            $this->historyIndex++;
            $this->command = $this->commandHistory[$this->historyIndex];
        } else {
            $this->historyIndex = count($this->commandHistory);
            $this->command = '';
        }
    }

    public function clearCommand()
    {
        $this->command = '';
    }

    public function handleTabCompletion()
    {
        // Get all available commands
        $allCommands = $this->commandRegistry->all();
        $commandNames = $allCommands->keys()->toArray();

        // If command is empty, show all commands
        if (empty($this->command)) {
            $this->suggestions = $commandNames;
            $this->showSuggestions = true;
            return;
        }

        // Find commands that match the current input
        $matches = [];
        foreach ($commandNames as $name) {
            if (strpos($name, $this->command) === 0) {
                $matches[] = $name;
            }
        }

        // If we have exactly one match, complete the command
        if (count($matches) === 1) {
            $this->command = $matches[0];
            $this->showSuggestions = false;
            return;
        }

        // If we have multiple matches, show suggestions
        if (count($matches) > 1) {
            $this->suggestions = $matches;
            $this->showSuggestions = true;
            return;
        }

        // No matches
        $this->showSuggestions = false;
    }

    public function selectSuggestion($suggestion)
    {
        $this->command = $suggestion;
        $this->showSuggestions = false;
    }

    protected function formatWelcomeMessage(): string
    {
        $date = date('D M j H:i:s T Y');

        return "Welcome to calkeOS v1.0.0 (GNU/Linux 6.9.420 x86_64)\n\n" .
            "* Documentation:  https://docs.calkeos.dev\n" .
            "* Management:     https://manage.calkeos.dev\n" .
            "* Support:        https://support.calkeos.dev (Premium support available!)\n\n" .
            "System information as of " . $date . "\n\n" .
            "╔════════════════════════════════════════════════════════════════════════════╗\n" .
            "║                           calkeOS Terminal v1.0.0                           ║\n" .
            "║                                                                           ║\n" .
            "║  • Now with 100% more terminal!                                          ║\n" .
            "║  • Featuring the revolutionary \"help\" command                             ║\n" .
            "║  • Includes state-of-the-art \"clear\" technology                          ║\n" .
            "║  • Powered by pure caffeine and determination                            ║\n" .
            "╚════════════════════════════════════════════════════════════════════════════╝\n\n" .
            "Type 'help' to see available commands.";
    }

    public function render()
    {
        return view('livewire.terminal')
            ->layout('components.layouts.app');
    }
}
