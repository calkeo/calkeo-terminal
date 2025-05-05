<?php

namespace App\Livewire;

use App\Commands\CommandParser;
use App\Commands\CommandRegistry;
use App\Commands\CommandState;
use App\Commands\CommandStates;
use App\Commands\WelcomeMessage;
use Illuminate\Support\Facades\Log;
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
    public $replaceLastOutput = false;
    public $lastOutput = [];
    public $formattedCommand = '';

    protected $commandRegistry;
    protected $commandParser;
    protected $commandState;

    public function boot(CommandRegistry $registry, CommandParser $parser)
    {
        $this->commandRegistry = $registry;
        $this->commandParser = $parser;
        $this->commandState = new CommandState();
        $this->formattedCommand = '';
    }

    public function mount()
    {
        if (request()->attributes->get('is_bot', false)) {
            return $this->render();
        }

        // Check if user is logged in
        if (!session('terminal_logged_in')) {
            return $this->redirect('/login');
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
        $this->commandState->clear();
        $this->replaceLastOutput = false;
        $this->lastOutput = [];
        // Add welcome message
        $welcomeMessage = new WelcomeMessage();
        $this->output[] = $welcomeMessage->format();
    }

    public function exception($e, $stopPropagation)
    {
        \Sentry\captureException($e);

        $this->output[] = "<span class=\"text-red-400\">Oops! Something went wrong and my creator has been notified.</span>";
        $this->output[] = "<span class=\"text-red-400\">Rest assured, he WILL be disciplined.</span>";

        Log::error($e);

        if (in_array(app()->environment(), ['production', 'testing'])) {
            $stopPropagation();
        }

    }

    public function executeCommand()
    {
        if (empty($this->command)) {
            return;
        }

        $this->showSuggestions = false;

        // Add command to output
        $this->formattedCommand = "<span class='text-cyan-400'>$</span> <span class='text-green-400'>" . htmlspecialchars($this->command) . "</span>";

        $this->stream(
            to: 'output',
            content: $this->formattedCommand,
        );

        $this->output[] = $this->formattedCommand;

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
            $result = $command->execute($this, $args);

            // Handle special clear command
            if ($this->commandState->has(CommandStates::CLEAR)) {
                $this->output = [(new WelcomeMessage())->format()];
                $this->currentCommandName = null;
                return;
            }

            // Handle logout command
            if ($this->commandState->has(CommandStates::LOGOUT)) {
                $this->output = $result;
                $this->currentCommandName = null;
                return $this->redirect('/login');
            }

            // Check if the command is complete (no more steps)
            if (!$this->commandState->has(CommandStates::INTERACTIVE)) {
                $this->currentCommandName = null;
            }

            // Process delayed output
            if ($this->isDelayedResponse($result)) {
                $this->hideInput = true;
                $this->js('$wire.delayedOutput(' . json_encode($result) . ');');
                return;
            }

            if ($this->replaceLastOutput) {
                // Find where the last output starts, including the command prompt line
                $lastOutputStart = count($this->output) - count($this->lastOutput) - 1;
                if ($lastOutputStart < 0) {
                    $lastOutputStart = 0;
                }

                // Keep everything before the last output
                $this->output = array_slice($this->output, 0, $lastOutputStart);
                $this->replaceLastOutput = false;
            }

            // Add the new output
            foreach ($result as $line) {
                $this->output[] = $line;
            }

            $this->lastOutput = $result;
        } else {
            $this->output[] = "<span class=\"text-red-400\">Command not found: {$commandName}</span>";
            $this->output[] = "<span class=\"text-yellow-400\">Type 'help' to see available commands.</span>";
        }
    }

    public function replaceLastOutput()
    {
        $this->replaceLastOutput = true;
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

        if ($this->replaceLastOutput) {
            // Find where the last output starts
            $lastOutputStart = count($this->output) - count($this->lastOutput);
            if ($lastOutputStart < 0) {
                $lastOutputStart = 0;
            }

            // Keep everything before the last output
            $this->output = array_slice($this->output, 0, $lastOutputStart);
            $this->replaceLastOutput = false;
        }

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

        $this->lastOutput = $result;
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
        $this->commandState->clear();

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

    /**
     * Get the command state
     *
     * @return CommandState
     */
    public function getCommandState(): CommandState
    {
        return $this->commandState;
    }

    public function viewPlainText()
    {
        Session::put('view_plain_text', true);
        return redirect()->to('/');
    }

    public function render()
    {
        if (request()->attributes->get('is_bot', false)) {
            return view('bot.terminal')->layout('layouts.empty');
        }
        return view('livewire.terminal');
    }
}
