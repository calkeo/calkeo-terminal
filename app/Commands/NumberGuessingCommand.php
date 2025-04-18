<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;

class NumberGuessingCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'numberguess';
    protected $description = 'Play Number Guessing Game';
    protected $aliases = ['numberguessing', 'guess'];
    protected $hidden = true;

    // Session keys
    protected const TARGET_NUMBER_KEY = 'target_number';
    protected const ATTEMPTS_KEY = 'attempts';
    protected const MIN_NUMBER_KEY = 'min_number';
    protected const MAX_NUMBER_KEY = 'max_number';
    protected const GAME_OVER_KEY = 'game_over';

    // Step definitions
    protected const STEP_DIFFICULTY = 1;
    protected const STEP_GUESS = 2;
    protected const STEP_PLAY_AGAIN = 3;

    // Difficulty levels
    protected const DIFFICULTY_EASY = 'easy';
    protected const DIFFICULTY_MEDIUM = 'medium';
    protected const DIFFICULTY_HARD = 'hard';

    public function execute(array $args = []): array
    {
        // Get current step from session
        $step = $this->getCurrentStep();

        // If we have arguments or are in the middle of a process, handle the step
        if (!empty($args) || $step > 1) {
            return $this->handleStep($args, $step);
        }

        // Start the interactive process
        return $this->startInteractiveProcess();
    }

    protected function getSessionKeys(): array
    {
        return [
            self::TARGET_NUMBER_KEY,
            self::ATTEMPTS_KEY,
            self::MIN_NUMBER_KEY,
            self::MAX_NUMBER_KEY,
            self::GAME_OVER_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_DIFFICULTY);

        return $this->interactiveOutput([
            $this->formatOutput("Number Guessing Game", 'header'),
            $this->formatOutput("====================", 'subheader'),
            "",
            $this->formatOutput("Choose difficulty level:", 'info'),
            $this->formatOutput("1. Easy (1-50, 10 attempts)", 'normal'),
            $this->formatOutput("2. Medium (1-100, 7 attempts)", 'normal'),
            $this->formatOutput("3. Hard (1-200, 5 attempts)", 'normal'),
            "",
            $this->formatOutput("Enter your choice (1-3):", 'warning'),
            $this->formatOutput("> ", 'command'),
        ]);
    }

    protected function handleStep(array $args, int $step): array
    {
        $input = implode(' ', $args);

        switch ($step) {
            case self::STEP_DIFFICULTY:
                return $this->handleDifficultyStep($input);
            case self::STEP_GUESS:
                return $this->handleGuessStep($input);
            case self::STEP_PLAY_AGAIN:
                return $this->handlePlayAgainStep($input);
            default:
                return [
                    $this->formatOutput("Error: Invalid step", 'error'),
                ];
        }
    }

    protected function handleDifficultyStep(string $input): array
    {
        // Validate input
        if (!in_array($input, ['1', '2', '3'])) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid choice! Please enter 1, 2, or 3:", 'error'),
                $this->formatOutput("> ", 'command'),
            ]);
        }

        // Set difficulty parameters
        switch ($input) {
            case '1': // Easy
                $minNumber = 1;
                $maxNumber = 50;
                $attempts = 10;
                $difficulty = self::DIFFICULTY_EASY;
                break;
            case '2': // Medium
                $minNumber = 1;
                $maxNumber = 100;
                $attempts = 7;
                $difficulty = self::DIFFICULTY_MEDIUM;
                break;
            case '3': // Hard
                $minNumber = 1;
                $maxNumber = 200;
                $attempts = 5;
                $difficulty = self::DIFFICULTY_HARD;
                break;
        }

        // Store game parameters
        $this->setSessionValue(self::MIN_NUMBER_KEY, $minNumber);
        $this->setSessionValue(self::MAX_NUMBER_KEY, $maxNumber);
        $this->setSessionValue(self::ATTEMPTS_KEY, $attempts);
        $this->setSessionValue(self::GAME_OVER_KEY, false);

        // Generate target number
        $targetNumber = rand($minNumber, $maxNumber);
        $this->setSessionValue(self::TARGET_NUMBER_KEY, $targetNumber);

        // Move to guess step
        $this->setCurrentStep(self::STEP_GUESS);

        // Show game instructions
        return $this->interactiveOutput([
            $this->formatOutput("Difficulty: " . ucfirst($difficulty), 'info'),
            $this->formatOutput("Range: {$minNumber} to {$maxNumber}", 'info'),
            $this->formatOutput("Attempts remaining: {$attempts}", 'info'),
            "",
            $this->formatOutput("I'm thinking of a number between {$minNumber} and {$maxNumber}.", 'white'),
            $this->formatOutput("Can you guess it?", 'white'),
            "",
            $this->formatOutput("Enter your guess:", 'warning'),
            $this->formatOutput("> ", 'command'),
        ]);
    }

    protected function handleGuessStep(string $input): array
    {
        // Check if game is over
        if ($this->getSessionValue(self::GAME_OVER_KEY, false)) {
            return $this->handlePlayAgainStep($input);
        }

        // Validate input
        if (!is_numeric($input)) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid input! Please enter a number:", 'error'),
                $this->formatOutput("> ", 'command'),
            ]);
        }

        $guess = (int) $input;
        $minNumber = $this->getSessionValue(self::MIN_NUMBER_KEY);
        $maxNumber = $this->getSessionValue(self::MAX_NUMBER_KEY);
        $targetNumber = $this->getSessionValue(self::TARGET_NUMBER_KEY);
        $attempts = $this->getSessionValue(self::ATTEMPTS_KEY);

        // Check if guess is within range
        if ($guess < $minNumber || $guess > $maxNumber) {
            return $this->interactiveOutput([
                $this->formatOutput("Your guess must be between {$minNumber} and {$maxNumber}!", 'error'),
                $this->formatOutput("Attempts remaining: {$attempts}", 'info'),
                "",
                $this->formatOutput("Enter your guess:", 'warning'),
                $this->formatOutput("> ", 'command'),
            ]);
        }

        // Decrease attempts
        $attempts--;
        $this->setSessionValue(self::ATTEMPTS_KEY, $attempts);

        // Check if guess is correct
        if ($guess === $targetNumber) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);

            return $this->interactiveOutput([
                $this->formatOutput("Congratulations! You've guessed the number {$targetNumber}!", 'success'),
                // TODO: Fix attempts count
                // $this->formatOutput("It took you " . ($this->getSessionValue(self::ATTEMPTS_KEY, 0) - $attempts) . " attempts.", 'info'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                $this->formatOutput("> ", 'command'),
            ]);
        }

        // Check if out of attempts
        if ($attempts <= 0) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);

            return $this->interactiveOutput([
                $this->formatOutput("Game Over! You've run out of attempts.", 'error'),
                $this->formatOutput("The number was {$targetNumber}.", 'info'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                $this->formatOutput("> ", 'command'),
            ]);
        }

        // Provide hint
        $hint = $guess < $targetNumber ? "low" : "high";

        return $this->interactiveOutput([
            $this->formatOutput("Your guess is too {$hint}!", 'info'),
            $this->formatOutput("Attempts remaining: {$attempts}", 'info'),
            "",
            $this->formatOutput("Enter your guess:", 'warning'),
            $this->formatOutput("> ", 'command'),
        ]);
    }

    protected function handlePlayAgainStep(string $input): array
    {
        $input = strtolower(trim($input));

        if ($input === 'yes' || $input === 'y') {
            // Reset to difficulty step
            $this->setCurrentStep(self::STEP_DIFFICULTY);

            return $this->interactiveOutput([
                $this->formatOutput("Great! Let's play again!", 'success'),
                "",
                $this->formatOutput("Choose difficulty level:", 'info'),
                $this->formatOutput("1. Easy (1-50, 10 attempts)", 'normal'),
                $this->formatOutput("2. Medium (1-100, 7 attempts)", 'normal'),
                $this->formatOutput("3. Hard (1-200, 5 attempts)", 'normal'),
                "",
                $this->formatOutput("Enter your choice (1-3):", 'warning'),
                $this->formatOutput("> ", 'command'),
            ]);
        } else {
            // End the game
            $this->clearSession();

            return [
                $this->formatOutput("Thanks for playing!", 'success'),
                $this->formatOutput("Run 'numberguess' to play again.", 'info'),
            ];
        }
    }
}
