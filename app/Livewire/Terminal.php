<?php

namespace App\Livewire;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use Illuminate\Support\Facades\Session;
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
    public $currentCommandName = null;

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

        $this->output = [];
        $this->commandHistory = [];
        $this->historyIndex = -1;
        $this->suggestions = [];
        $this->showSuggestions = false;
        $this->currentCommandName = null;

        // Add welcome message
        $welcomeMessage = new \App\Commands\WelcomeMessage();
        $this->output[] = $welcomeMessage->format();
    }

    public function executeCommand()
    {
        if (empty($this->command)) {
            return;
        }

        $this->showSuggestions = false;

        // Add command to output
        $this->output[] = "<span class='text-cyan-400'>$</span> <span class='text-green-400'>" . htmlspecialchars($this->command) . "</span>";

        // Add command to history with timestamp
        $history = Session::get('command_history', []);
        $history[] = [
            'command' => $this->command,
            'timestamp' => now()->format('Y-m-d H:i:s'),
        ];
        Session::put('command_history', $history);

        // Add command to history
        $this->commandHistory[] = $this->command;
        $this->historyIndex = count($this->commandHistory);

        // If we have a current command, treat the input as args for that command
        if ($this->currentCommandName) {
            $command = $this->commandRegistry->get($this->currentCommandName);
            $args = [$this->command];
        } else {
            // Parse the command
            $parsed = $this->commandParser->parse($this->command);
            $commandName = $parsed['command'];
            $args = $parsed['args'];

            // Handle empty command
            if (empty($commandName)) {
                return;
            }

            // Find the command
            $command = $this->commandRegistry->get($commandName);
            if ($command) {
                $this->currentCommandName = $commandName;
            }
        }

        // Clear the command input
        $this->command = '';

        if ($command) {
            $result = $command->execute($args);

            // Handle special clear command
            if ($result === ['__CLEAR__']) {
                $this->output = [$this->formatWelcomeMessage()];
                $this->currentCommandName = null;
                return;
            }

            // Handle logout command
            if (in_array('__LOGOUT__', $result)) {
                $this->output = array_merge($this->output, array_diff($result, ['__LOGOUT__']));
                $this->currentCommandName = null;
                return $this->redirect('/');
            }

            // Check if the command is complete (no more steps)
            if (!in_array('__INTERACTIVE__', $result)) {
                $this->currentCommandName = null;
            } else {
                $result = array_diff($result, ['__INTERACTIVE__']);
            }

            $this->output = array_merge($this->output, $result);
        } else {
            $this->output[] = "<span class=\"text-red-400\">calkeos: command not found: {$commandName}</span>";
            $this->output[] = "<span class=\"text-yellow-400\">Type 'help' to see available commands.</span>";
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

        $html = '<div class="border border-gray-700 my-2">';

        // Header
        $html .= '<div class="bg-gray-800 px-2 py-1 border-b border-gray-700 flex items-center justify-center">';
        $html .= '<span class="text-cyan-400 font-bold">calkeOS Terminal v1.0.0</span>';
        $html .= '</div>';

        // Content
        $html .= '<div class="p-2">';

        // Features
        $html .= '<div class="py-1">';
        $html .= '<span class="text-yellow-400">*</span> Now with 100% more terminal!<br>';
        $html .= '<span class="text-yellow-400">*</span> Featuring the revolutionary "help" command<br>';
        $html .= '<span class="text-yellow-400">*</span> Includes state-of-the-art "clear" technology<br>';
        $html .= '<span class="text-yellow-400">*</span> Powered by pure caffeine and determination';
        $html .= '</div>';

        // System info
        $html .= '<div class="py-1 mt-2">';
        $html .= 'System information as of <span class="text-cyan-400">' . $date . '</span><br>';
        $html .= 'Kernel: <span class="text-blue-400">6.9.420</span> (GNU/Linux x86_64)<br>';
        $html .= 'CPU: <span class="text-blue-400">Intel(R) Caffeine(TM) i9 9999K @ 4.20GHz</span><br>';
        $html .= 'Memory: <span class="text-blue-400">42GB of pure determination</span><br>';
        $html .= 'Disk: <span class="text-blue-400">1TB of possibilities</span>';
        $html .= '</div>';

        // Help text
        $html .= '<div class="py-1 mt-2">';
        $html .= 'Type <span class="text-purple-400">help</span> to see available commands';
        $html .= '</div>';

        $html .= '</div>'; // End content
        $html .= '</div>'; // End box

        // Welcome message
        $html .= '<div class="my-2">';
        $html .= 'Welcome to <span class="text-cyan-400">calkeOS v1.0.0</span> (GNU/Linux 6.9.420 x86_64)<br><br>';
        $html .= '<span class="text-yellow-400">*</span> Documentation: <span class="text-blue-400">https://docs.calkeos.dev</span><br>';
        $html .= '<span class="text-yellow-400">*</span> Management: <span class="text-blue-400">https://manage.calkeos.dev</span><br>';
        $html .= '<span class="text-yellow-400">*</span> Support: <span class="text-blue-400">https://support.calkeos.dev</span> <span class="text-pink-400">(Premium support available!)</span>';
        $html .= '</div>';

        return $html;
    }

    public function render()
    {
        return view('livewire.terminal')->layout('components.layouts.app');
    }
}