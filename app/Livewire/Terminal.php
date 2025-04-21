<?php

namespace App\Livewire;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Commands\WelcomeMessage;
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
    public $isProcessingDelayedOutput = false;
    public $hideInput = false;

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

        $this->commandRegistry->resetStaleInteractiveCommands();

        // Get username from session
        $this->username = session('terminal_username', 'guest');

        $this->output = [];
        $this->commandHistory = [];
        $this->historyIndex = -1;
        $this->suggestions = [];
        $this->showSuggestions = false;
        $this->currentCommandName = null;
        $this->isProcessingDelayedOutput = false;
        $this->hideInput = false;

        // Add welcome message
        $welcomeMessage = new WelcomeMessage();
        $this->output[] = $welcomeMessage->format();
    }

    public function exception($e, $stopPropagation)
    {
        \Sentry\captureException($e);

        $this->output[] = "<span class=\"text-red-400\">Oops! Something went wrong and my creator has been notified.</span>";
        $this->output[] = "<span class=\"text-red-400\">Rest assured, he WILL be disciplined.</span>";

        $stopPropagation();
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
                $this->output = [(new WelcomeMessage())->format()];
                $this->currentCommandName = null;
                return;
            }

            // Handle logout command
            if (in_array('__LOGOUT__', $result)) {
                $this->output = array_merge($this->output, array_diff($result, ['__LOGOUT__']));
                $this->currentCommandName = null;
                return $this->redirect('/login');
            }

            // Check if the command is complete (no more steps)
            if (!in_array('__INTERACTIVE__', $result)) {
                $this->currentCommandName = null;
            } else {
                $result = array_diff($result, ['__INTERACTIVE__']);
            }

            // Process delayed output
            if ($this->isDelayedResponse($result)) {
                $this->hideInput = true;
                $this->js('$wire.delayedOutput(' . json_encode($result) . ');');
                return;
            }

            foreach ($result as $line) {
                $this->output[] = $line;
            }

        } else {
            $this->output[] = "<span class=\"text-red-400\">Command not found: {$commandName}</span>";
            $this->output[] = "<span class=\"text-yellow-400\">Type 'help' to see available commands.</span>";
        }
    }

    public function isDelayedResponse(array $result): bool
    {

        foreach ($result as $line) {
            if (is_array($line) && isset($line['type']) && $line['type'] === 'delayed') {
                return true;
            }
        }
        return false;
    }

    public function delayedOutput(array $result)
    {
        $this->isProcessingDelayedOutput = true;

        foreach ($result as $line) {
            if (is_array($line) && isset($line['type']) && $line['type'] === 'delayed') {
                sleep($line['delay'] / 1000);

                $content = $this->wrapLineContent($line['content']);

                $this->output[] = $line['content'];

                $this->stream(
                    to: 'output',
                    content: $content,
                );
            } else {
                $this->output[] = $line;
            }
        }

        $this->isProcessingDelayedOutput = false;
        $this->hideInput = false;
        $this->dispatch('focusInput');
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
        $this->currentCommandName = null;

        $this->commandRegistry->resetStaleInteractiveCommands();
    }

    public function handleTabCompletion()
    {
        // Get all available commands
        $allCommands = $this->commandRegistry->all();
        // Filter out hidden commands
        $allCommands = $allCommands->filter(function ($command) {
            return !$command->isHidden();
        });
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

    protected function wrapLineContent($content)
    {
        return "<div class='whitespace-pre-wrap leading-relaxed'>" . $content . "</div>";
    }

    public function render()
    {
        return view('livewire.terminal');
    }
}
