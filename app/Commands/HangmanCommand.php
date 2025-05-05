<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Cache;

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
    protected const DICTIONARY_KEY = 'dictionary';

    // Step definitions
    protected const STEP_START = 1;
    protected const STEP_DICTIONARY = 2;
    protected const STEP_DIFFICULTY = 3;
    protected const STEP_GUESS = 4;
    protected const STEP_PLAY_AGAIN = 5;

    // Difficulty levels
    protected const DIFFICULTY_EASY = 'easy';
    protected const DIFFICULTY_MEDIUM = 'medium';
    protected const DIFFICULTY_HARD = 'hard';

    // Dictionary settings
    protected const MIN_WORD_LENGTH = 3;
    protected const MAX_WORD_LENGTH_EASY = 7;
    protected const MAX_WORD_LENGTH_MEDIUM = 10;
    protected const MAX_WORD_LENGTH_HARD = 20;
    protected const CACHE_KEY = 'hangman_dictionary_v02';
    protected const CACHE_TTL = 60 * 60 * 24 * 30; // 30 days
    protected const DICTIONARIES = [
        'GB' => 'en_GB-ise.dic',
        'US' => 'en_US.dic',
        'CA' => 'en_CA.dic',
        'AU' => 'en_AU.dic',
    ];

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
    protected $dictionary = [];

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

        $this->loadDictionary();

        // Get current step from session
        $step = $this->getCurrentStep();

        // If we have arguments or are in the middle of a process, handle the step
        if (!empty($args) || $step > 1) {
            return $this->handleStep($args, $step);
        }

        // Start the interactive process
        return $this->startInteractiveProcess();
    }

    protected function loadDictionary(): void
    {
        // Try to get dictionary from cache
        $cacheKey = self::CACHE_KEY . '_' . $this->getSessionValue(self::DICTIONARY_KEY, 'GB');
        $this->dictionary = Cache::get($cacheKey);

        if ($this->dictionary === null) {
            // Get selected dictionary
            $dictionaryKey = $this->getSessionValue(self::DICTIONARY_KEY, 'GB');
            $dictionaryFile = self::DICTIONARIES[$dictionaryKey];

            // Load dictionary file
            $dictionaryPath = resource_path('dictionary/' . $dictionaryFile);
            if (!file_exists($dictionaryPath)) {
                throw new \RuntimeException('Dictionary file not found at: ' . $dictionaryPath);
            }

            $words = file($dictionaryPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

            // Skip the first line which contains the word count
            array_shift($words);

            // Process words and organize by length
            $this->dictionary = [];
            foreach ($words as $word) {
                // Remove any slash and following characters (used in .dic files for affix rules)
                $word = preg_replace('/\/.*$/', '', $word);

                // Skip words that are too short
                if (strlen($word) < self::MIN_WORD_LENGTH) {
                    continue;
                }

                $length = strlen($word);
                if (!isset($this->dictionary[$length])) {
                    $this->dictionary[$length] = [];
                }
                $this->dictionary[$length][] = strtolower($word);
            }

            // Cache the processed dictionary
            Cache::put($cacheKey, $this->dictionary, self::CACHE_TTL);
        }
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
            self::DICTIONARY_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_DICTIONARY);

        return $this->interactiveOutput([
            $this->formatOutput("Hangman Game", 'header'),
            $this->formatOutput("============", 'subheader'),
            "",
            $this->formatOutput("Choose your dictionary:", 'info'),
            $this->formatOutput("1. British English (GB)", 'normal'),
            $this->formatOutput("2. American English (US)", 'normal'),
            $this->formatOutput("3. Canadian English (CA)", 'normal'),
            $this->formatOutput("4. Australian English (AU)", 'normal'),
            "",
            $this->formatOutput("Enter your choice (1-4):", 'warning'),
        ]);
    }

    protected function handleStep(array $args, int $step): array
    {
        $input = implode('', $args);

        switch ($step) {
            case self::STEP_DICTIONARY:
                return $this->handleDictionaryStep($input);
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

    protected function handleDictionaryStep(string $input): array
    {
        if (!in_array($input, ['1', '2', '3', '4'])) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid choice! Please enter 1, 2, 3, or 4:", 'error'),
            ]);
        }

        // Set dictionary
        $dictionary = match ($input) {
            '1' => 'GB',
            '2' => 'US',
            '3' => 'CA',
            '4' => 'AU',
        };
        $this->setSessionValue(self::DICTIONARY_KEY, $dictionary);

        $this->loadDictionary();

        $this->setCurrentStep(self::STEP_DIFFICULTY);

        return $this->interactiveOutput([
            $this->formatOutput("Dictionary: " . $dictionary, 'info'),
            "",
            $this->formatOutput("Choose difficulty level:", 'info'),
            $this->formatOutput("1. Easy (Short words)", 'normal'),
            $this->formatOutput("2. Medium (Medium words)", 'normal'),
            $this->formatOutput("3. Hard (Long words)", 'normal'),
            "",
            $this->formatOutput("Enter your choice (1-3):", 'warning'),
        ]);
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
            $this->setCurrentStep(self::STEP_DICTIONARY);

            return $this->interactiveOutput([
                $this->formatOutput("Let's play again!", 'success'),
                "",
                $this->formatOutput("Choose your dictionary:", 'info'),
                $this->formatOutput("1. British English (GB)", 'normal'),
                $this->formatOutput("2. American English (US)", 'normal'),
                $this->formatOutput("3. Canadian English (CA)", 'normal'),
                $this->formatOutput("4. Australian English (AU)", 'normal'),
                "",
                $this->formatOutput("Enter your choice (1-4):", 'warning'),
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
        // Define length ranges for each difficulty
        $lengthRanges = [
            self::DIFFICULTY_EASY => [
                'min' => self::MIN_WORD_LENGTH,
                'max' => self::MAX_WORD_LENGTH_EASY,
            ],
            self::DIFFICULTY_MEDIUM => [
                'min' => self::MAX_WORD_LENGTH_EASY + 1,
                'max' => self::MAX_WORD_LENGTH_MEDIUM,
            ],
            self::DIFFICULTY_HARD => [
                'min' => self::MAX_WORD_LENGTH_MEDIUM + 1,
                'max' => self::MAX_WORD_LENGTH_HARD,
            ],
        ];

        $range = $lengthRanges[$difficulty];

        // Get all words that match the length criteria
        $availableWords = [];
        for ($length = $range['min']; $length <= $range['max']; $length++) {
            if (isset($this->dictionary[$length])) {
                $availableWords = array_merge($availableWords, $this->dictionary[$length]);
            }
        }

        if (empty($availableWords)) {
            // Fallback words if dictionary is empty
            $fallbackWords = [
                self::DIFFICULTY_EASY => ['cat', 'dog', 'sun', 'moon', 'star'],
                self::DIFFICULTY_MEDIUM => ['computer', 'elephant', 'giraffe', 'mountain', 'ocean'],
                self::DIFFICULTY_HARD => ['extravaganza', 'bureaucracy', 'chrysanthemum', 'incomprehensible', 'juxtaposition'],
            ];
            return $fallbackWords[$difficulty][array_rand($fallbackWords[$difficulty])];
        }

        return $availableWords[array_rand($availableWords)];
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
