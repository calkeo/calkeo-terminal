<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;
use Faker\Factory;

class HangmanCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'hangman';
    protected $description = 'Play Hangman Game';
    protected $aliases = ['hangman'];
    protected $hidden = true;

    // Session keys
    protected const WORD_KEY = 'word';
    protected const GUESSED_LETTERS_KEY = 'guessed_letters';
    protected const REMAINING_GUESSES_KEY = 'remaining_guesses';
    protected const GAME_OVER_KEY = 'game_over';
    protected const WINNER_KEY = 'winner';
    protected const DIFFICULTY_KEY = 'difficulty';

    // Step definitions
    protected const STEP_START = 1;
    protected const STEP_DIFFICULTY = 2;
    protected const STEP_GUESS = 3;
    protected const STEP_PLAY_AGAIN = 4;

    // Difficulty levels
    protected const DIFFICULTY_EASY = 'easy';
    protected const DIFFICULTY_MEDIUM = 'medium';
    protected const DIFFICULTY_HARD = 'hard';

    // Hangman visual states
    protected const HANGMAN_STATES = [
        6 => "
            +---+
            |   |
                |
                |
                |
                |
           =========",
        5 => "
            +---+
            |   |
            O   |
                |
                |
                |
           =========",
        4 => "
            +---+
            |   |
            O   |
            |   |
                |
                |
           =========",
        3 => "
            +---+
            |   |
            O   |
           /|   |
                |
                |
           =========",
        2 => "
            +---+
            |   |
            O   |
           /|\\  |
                |
                |
           =========",
        1 => "
            +---+
            |   |
            O   |
           /|\\  |
           /    |
                |
           =========",
        0 => "
            +---+
            |   |
            O   |
           /|\\  |
           / \\  |
                |
           =========",
    ];

    protected $terminal;
    protected $faker;

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

        $this->faker = Factory::create();

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
            self::WORD_KEY,
            self::GUESSED_LETTERS_KEY,
            self::REMAINING_GUESSES_KEY,
            self::GAME_OVER_KEY,
            self::WINNER_KEY,
            self::DIFFICULTY_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_DIFFICULTY);

        return $this->interactiveOutput([
            $this->formatOutput("Hangman Game", 'header'),
            $this->formatOutput("============", 'subheader'),
            "",
            $this->formatOutput("Choose difficulty level:", 'info'),
            $this->formatOutput("1. Easy (Short words)", 'normal'),
            $this->formatOutput("2. Medium (Medium words)", 'normal'),
            $this->formatOutput("3. Hard (Long words)", 'normal'),
            "",
            $this->formatOutput("Enter your choice (1-3):", 'warning'),
        ]);
    }

    protected function handleStep(array $args, int $step): array
    {
        $input = implode('', $args);

        switch ($step) {
            case self::STEP_DIFFICULTY:
                return $this->handleDifficultyStep($input);
            case self::STEP_GUESS:
                $this->terminal->replaceLastOutput();
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
        if (!in_array($input, ['1', '2', '3'])) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid choice! Please enter 1, 2, or 3:", 'error'),
            ]);
        }

        // Set difficulty
        $difficulty = match ($input) {
            '1' => self::DIFFICULTY_EASY,
            '2' => self::DIFFICULTY_MEDIUM,
            '3' => self::DIFFICULTY_HARD,
        };
        $this->setSessionValue(self::DIFFICULTY_KEY, $difficulty);

        // Initialize game
        $word = $this->getRandomWord($difficulty);
        $this->setSessionValue(self::WORD_KEY, $word);
        $this->setSessionValue(self::GUESSED_LETTERS_KEY, []);
        $this->setSessionValue(self::REMAINING_GUESSES_KEY, 6); // 6 guesses for hangman
        $this->setSessionValue(self::GAME_OVER_KEY, false);
        $this->setSessionValue(self::WINNER_KEY, null);

        $this->setCurrentStep(self::STEP_GUESS);

        return $this->interactiveOutput([
            $this->formatOutput("Difficulty: " . ucfirst($difficulty), 'info'),
            "",
            $this->formatOutput("Word to guess:", 'info'),
            $this->formatWord($word, []),
            "",
            $this->formatOutput("Enter a letter:", 'warning'),
        ]);
    }

    protected function handleGuessStep(string $input): array
    {
        // Check if game is over
        if ($this->getSessionValue(self::GAME_OVER_KEY, false)) {
            return $this->handlePlayAgainStep($input);
        }

        $word = $this->getSessionValue(self::WORD_KEY);
        $guessedLetters = $this->getSessionValue(self::GUESSED_LETTERS_KEY);
        $remainingGuesses = $this->getSessionValue(self::REMAINING_GUESSES_KEY);

        // Validate input
        if (strlen($input) !== 1 || !ctype_alpha($input)) {
            return $this->interactiveOutput([
                $this->formatOutput($this->getHangmanVisual($remainingGuesses), 'normal'),
                $this->formatOutput('', 'normal'),
                $this->formatOutput("Invalid input! Please enter a single letter:", 'error'),
                $this->formatWord($word, $guessedLetters),
                $this->formatOutput("Guessed letters: " . implode(', ', $guessedLetters), 'info'),
                $this->formatOutput("Remaining guesses: " . $remainingGuesses, 'info'),
                "",
                $this->formatOutput("Enter a letter:", 'warning'),
            ]);
        }

        $letter = strtolower($input);

        // Check if letter was already guessed
        if (in_array($letter, $guessedLetters)) {
            return $this->interactiveOutput([
                $this->formatOutput($this->getHangmanVisual($remainingGuesses), 'normal'),
                $this->formatOutput('', 'normal'),
                $this->formatOutput("You already guessed that letter!", 'error'),
                $this->formatWord($word, $guessedLetters),
                $this->formatOutput("Guessed letters: " . implode(', ', $guessedLetters), 'info'),
                $this->formatOutput("Remaining guesses: " . $remainingGuesses, 'info'),
                "",
                $this->formatOutput("Enter a letter:", 'warning'),
            ]);
        }

        // Add letter to guessed letters
        $guessedLetters[] = $letter;
        $this->setSessionValue(self::GUESSED_LETTERS_KEY, $guessedLetters);

        // Check if letter is in word
        if (strpos($word, $letter) === false) {
            $remainingGuesses--;
            $this->setSessionValue(self::REMAINING_GUESSES_KEY, $remainingGuesses);
        }

        // Check for win
        if ($this->isWordGuessed($word, $guessedLetters)) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);
            $this->setSessionValue(self::WINNER_KEY, 'player');

            return $this->interactiveOutput([
                $this->formatWord($word, $guessedLetters),
                "",
                $this->formatOutput("Congratulations! You win!", 'success'),
                $this->formatOutput("The word was: " . strtoupper($word), 'info'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
            ]);
        }

        // Check for loss
        if ($remainingGuesses <= 0) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);
            $this->setSessionValue(self::WINNER_KEY, 'computer');

            return $this->interactiveOutput([
                $this->formatWord($word, $guessedLetters),
                "",
                $this->formatOutput("Game Over! You lose!", 'error'),
                $this->formatOutput("The word was: " . strtoupper($word), 'info'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
            ]);
        }

        return $this->interactiveOutput([
            $this->formatOutput($this->getHangmanVisual($remainingGuesses), 'normal'),
            $this->formatOutput('', 'normal'),
            $this->formatOutput("Word: " . $this->formatWord($word, $guessedLetters), 'info'),
            $this->formatOutput("Guessed letters: " . implode(', ', $guessedLetters), 'info'),
            $this->formatOutput("Remaining guesses: " . $remainingGuesses, 'warning'),
            $this->formatOutput('', 'normal'),
            $this->formatOutput("Enter a letter:", 'warning'),
        ]);
    }

    protected function handlePlayAgainStep(string $input): array
    {
        $input = strtolower(trim($input));

        if ($input === 'yes' || $input === 'y') {
            // Reset to difficulty step
            $this->setCurrentStep(self::STEP_DIFFICULTY);

            return $this->interactiveOutput([
                $this->formatOutput("Let's play again!", 'success'),
                "",
                $this->formatOutput("Choose difficulty level:", 'info'),
                $this->formatOutput("1. Easy (Short words)", 'normal'),
                $this->formatOutput("2. Medium (Medium words)", 'normal'),
                $this->formatOutput("3. Hard (Long words)", 'normal'),
                "",
                $this->formatOutput("Enter your choice (1-3):", 'warning'),
            ]);
        } else {
            // End the game
            $this->clearSession();

            return [
                $this->formatOutput("Thanks for playing!", 'success'),
                $this->formatOutput("Run 'hangman' to play again.", 'info'),
            ];
        }
    }

    protected function getRandomWord(string $difficulty): string
    {
        $word = '';
        $attempts = 0;
        $maxAttempts = 10;

        while ($attempts < $maxAttempts) {
            // Get a small paragraph of text
            $text = $this->faker->realText(50);
            // Split into words and filter by length
            $words = array_filter(
                preg_split('/\s+/', $text),
                function ($word) use ($difficulty) {
                    // Remove all punctuation and non-alphabetic characters
                    $word = preg_replace('/[^a-zA-Z]/', '', $word);
                    // Skip empty words or words that are too short
                    if (empty($word) || strlen($word) < 3) {
                        return false;
                    }

                    $length = strlen($word);
                    switch ($difficulty) {
                        case self::DIFFICULTY_EASY:
                            return $length >= 3 && $length <= 5;
                        case self::DIFFICULTY_MEDIUM:
                            return $length >= 6 && $length <= 8;
                        case self::DIFFICULTY_HARD:
                            return $length >= 9;
                        default:
                            return false;
                    }
                }
            );

            if (!empty($words)) {
                $word = strtolower($words[array_rand($words)]);
                // Final cleanup to ensure no punctuation remains
                return preg_replace('/[^a-zA-Z]/', '', $word);
            }
            $attempts++;
        }

        // Fallback words if Faker fails to generate appropriate words
        $fallbackWords = [
            self::DIFFICULTY_EASY => ['cat', 'dog', 'sun', 'moon', 'star'],
            self::DIFFICULTY_MEDIUM => ['computer', 'elephant', 'giraffe', 'mountain', 'ocean'],
            self::DIFFICULTY_HARD => ['extravaganza', 'bureaucracy', 'chrysanthemum', 'incomprehensible', 'juxtaposition'],
        ];

        return $fallbackWords[$difficulty][array_rand($fallbackWords[$difficulty])];
    }

    protected function formatWord(string $word, array $guessedLetters): string
    {
        $display = '';
        foreach (str_split($word) as $letter) {
            if (in_array($letter, $guessedLetters)) {
                $display .= $this->formatOutput(strtoupper($letter), 'success') . ' ';
            } else {
                $display .= '_ ';
            }
        }
        return $display;
    }

    protected function getHangmanVisual(int $remainingGuesses): string
    {
        return self::HANGMAN_STATES[$remainingGuesses];
    }

    protected function isWordGuessed(string $word, array $guessedLetters): bool
    {
        foreach (str_split($word) as $letter) {
            if (!in_array($letter, $guessedLetters)) {
                return false;
            }
        }
        return true;
    }
}
