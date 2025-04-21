<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;

class RockPaperScissorsCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'rps';
    protected $description = 'Play Rock, Paper, Scissors';
    protected $aliases = ['rockpaperscissors'];
    protected $hidden = true;

    // Session keys
    protected const CHOICE_KEY = 'choice';
    protected const COMPUTER_CHOICE_KEY = 'computer_choice';
    protected const RESULT_KEY = 'result';

    // Step definitions
    protected const STEP_CHOICE = 1;
    protected const STEP_RESULT = 2;
    protected const STEP_PLAY_AGAIN = 3;

    protected $terminal;

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        $this->terminal = $terminal;

        // Get current step from session
        $step = $this->getCurrentStep();

        // If we have arguments or are in the middle of a process, handle the step
        if (!empty($args) || $step > 1) {
            return $this->handleStep($args, $step);
        }

        // Start the interactive process
        return $this->startInteractiveProcess();
    }

    protected function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    protected function getSessionKeys(): array
    {
        return [
            self::CHOICE_KEY,
            self::COMPUTER_CHOICE_KEY,
            self::RESULT_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_CHOICE);

        return $this->interactiveOutput([
            $this->formatOutput("Rock, Paper, Scissors", 'header'),
            $this->formatOutput("====================", 'subheader'),
            "",
            $this->formatOutput("Make your choice:", 'info'),
            $this->formatOutput("1. Rock 🪨", 'normal'),
            $this->formatOutput("2. Paper 📄", 'normal'),
            $this->formatOutput("3. Scissors ✂️", 'normal'),
            "",
            $this->formatOutput("Enter your choice (1-3):", 'warning'),
        ]);
    }

    protected function handleStep(array $args, int $step): array
    {
        $input = implode(' ', $args);

        switch ($step) {
            case self::STEP_CHOICE:
                return $this->handleChoiceStep($input);
            case self::STEP_RESULT:
                return $this->handleResultStep($input);
            case self::STEP_PLAY_AGAIN:
                return $this->handlePlayAgainStep($input);
            default:
                return [
                    $this->formatOutput("Error: Invalid step", 'error'),
                ];
        }
    }

    protected function handleChoiceStep(string $input): array
    {
        // Validate input
        if (!in_array($input, ['1', '2', '3'])) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid choice! Please enter 1, 2, or 3:", 'error'),
            ]);
        }

        // Store player's choice
        $this->setSessionValue(self::CHOICE_KEY, $input);

        // Generate computer's choice
        $computerChoice = rand(1, 3);
        $this->setSessionValue(self::COMPUTER_CHOICE_KEY, $computerChoice);

        // Determine the result
        $result = $this->determineWinner($input, $computerChoice);
        $this->setSessionValue(self::RESULT_KEY, $result);

        // Move to result step
        $this->setCurrentStep(self::STEP_RESULT);

        // Show the result
        $choices = [
            '1' => 'Rock 🪨',
            '2' => 'Paper 📄',
            '3' => 'Scissors ✂️',
        ];

        $playerChoice = $choices[$input];
        $computerChoiceText = $choices[$computerChoice];

        $output = [
            $this->formatOutput("You chose: {$playerChoice}", 'info'),
            $this->formatOutput("Computer chose: {$computerChoiceText}", 'info'),
            "",
        ];

        if ($result === 'tie') {
            $output[] = $this->formatOutput("It's a tie! 🤝", 'white');
        } elseif ($result === 'win') {
            $output[] = $this->formatOutput('You win! 🎉', 'success');
        } else {
            $output[] = $this->formatOutput('Computer wins! 🤖', 'error');
        }

        $output[] = "";
        $output[] = $this->formatOutput("Want to play again? (yes/no):", 'warning');

        return $this->interactiveOutput($output);
    }

    protected function handleResultStep(string $input): array
    {
        $input = strtolower(trim($input));

        if ($input === 'yes' || $input === 'y') {
            // Reset to choice step
            $this->setCurrentStep(self::STEP_CHOICE);

            return $this->interactiveOutput([
                $this->formatOutput("Great! Let's play again!", 'success'),
                "",
                $this->formatOutput("Make your choice:", 'info'),
                $this->formatOutput("1. Rock 🪨", 'normal'),
                $this->formatOutput("2. Paper 📄", 'normal'),
                $this->formatOutput("3. Scissors ✂️", 'normal'),
                "",
                $this->formatOutput("Enter your choice (1-3):", 'warning'),
            ]);
        } else {
            // End the game
            $this->clearSession();

            return [
                $this->formatOutput("Thanks for playing!", 'success'),
                $this->formatOutput("Run 'rps' to play again.", 'info'),
            ];
        }
    }

    protected function handlePlayAgainStep(string $input): array
    {
        // This step is not used in the current implementation
        // but is kept for consistency with the trait
        return $this->handleResultStep($input);
    }

    protected function determineWinner(string $playerChoice, string $computerChoice): string
    {
        if ($playerChoice === $computerChoice) {
            return 'tie';
        }

        if (
            ($playerChoice === '1' && $computerChoice === '3') || // Rock beats Scissors
            ($playerChoice === '2' && $computerChoice === '1') || // Paper beats Rock
            ($playerChoice === '3' && $computerChoice === '2') // Scissors beats Paper
        ) {
            return 'win';
        }

        return 'lose';
    }
}
