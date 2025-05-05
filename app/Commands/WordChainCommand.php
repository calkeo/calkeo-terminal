<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Cache;

class WordChainCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'wordchain';
    protected $description = 'Play Word Chain Game';
    protected $aliases = ['chain'];
    protected $hidden = true;

    // Session keys
    protected const CHAIN_KEY = 'chain';
    protected const CURRENT_PLAYER_KEY = 'current_player';
    protected const GAME_OVER_KEY = 'game_over';
    protected const DIFFICULTY_KEY = 'difficulty';
    protected const USED_WORDS_KEY = 'used_words';
    protected const DICTIONARY_KEY = 'dictionary';

    // Step definitions
    protected const STEP_DICTIONARY = 1;
    protected const STEP_DIFFICULTY = 2;
    protected const STEP_PLAY = 3;
    protected const STEP_PLAY_AGAIN = 4;

    // Players
    protected const PLAYER_HUMAN = 'human';
    protected const PLAYER_COMPUTER = 'computer';

    // Difficulty levels
    protected const DIFFICULTY_EASY = 'easy';
    protected const DIFFICULTY_MEDIUM = 'medium';
    protected const DIFFICULTY_HARD = 'hard';

    // Dictionary settings
    protected const MIN_WORD_LENGTH = 3;
    protected const MAX_WORD_LENGTH_EASY = 5;
    protected const MAX_WORD_LENGTH_MEDIUM = 7;
    protected const MAX_WORD_LENGTH_HARD = 10;
    protected const CACHE_KEY = 'wordchain_dictionary_v1';
    protected const CACHE_TTL = 60 * 60 * 24 * 30; // 30 days
    protected const DICTIONARIES = [
        'GB' => 'en_GB-ise.dic',
        'US' => 'en_US.dic',
        'CA' => 'en_CA.dic',
        'AU' => 'en_AU.dic',
    ];

    // Game settings
    protected const MAX_CHAIN_LENGTH_EASY = 20;
    protected const MAX_CHAIN_LENGTH_MEDIUM = 30;
    protected const MAX_CHAIN_LENGTH_HARD = 40;
    protected const MIN_WORDS_FOR_VICTORY = 5;

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

            // Process words and organize by first letter
            $this->dictionary = [];
            foreach ($words as $word) {
                // Remove any slash and following characters (used in .dic files for affix rules)
                $word = preg_replace('/\/.*$/', '', $word);

                // Skip words that are too short
                if (strlen($word) < self::MIN_WORD_LENGTH) {
                    continue;
                }

                $firstLetter = strtolower($word[0]);
                if (!isset($this->dictionary[$firstLetter])) {
                    $this->dictionary[$firstLetter] = [];
                }
                $this->dictionary[$firstLetter][] = strtolower($word);
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
            self::CHAIN_KEY,
            self::CURRENT_PLAYER_KEY,
            self::GAME_OVER_KEY,
            self::DIFFICULTY_KEY,
            self::USED_WORDS_KEY,
            self::DICTIONARY_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_DICTIONARY);

        return $this->interactiveOutput([
            $this->formatOutput("Word Chain Game", 'header'),
            $this->formatOutput("==============", 'subheader'),
            $this->formatOutput(""),
            $this->formatOutput("Rules:", 'info'),
            $this->formatOutput("1. Players take turns entering words", 'normal'),
            $this->formatOutput("2. Each word must begin with the last letter of the previous word", 'normal'),
            $this->formatOutput("3. Words cannot be reused", 'normal'),
            $this->formatOutput("4. Words must be valid English words", 'normal'),
            $this->formatOutput("5. Chain length needed to win:", 'normal'),
            $this->formatOutput("   Easy: " . self::MAX_CHAIN_LENGTH_EASY . " words", 'normal'),
            $this->formatOutput("   Medium: " . self::MAX_CHAIN_LENGTH_MEDIUM . " words", 'normal'),
            $this->formatOutput("   Hard: " . self::MAX_CHAIN_LENGTH_HARD . " words", 'normal'),
            $this->formatOutput("&nbsp;", 'normal'),
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
            case self::STEP_PLAY:
                $this->terminal->replaceLastOutput();
                return $this->handlePlayStep($input);
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

        $this->setCurrentStep(self::STEP_DIFFICULTY);

        return $this->interactiveOutput([
            $this->formatOutput("Dictionary: " . $dictionary, 'info'),
            "",
            $this->formatOutput("Choose difficulty level:", 'info'),
            $this->formatOutput("1. Easy (Common words)", 'normal'),
            $this->formatOutput("2. Medium (Wider vocabulary)", 'normal'),
            $this->formatOutput("3. Hard (Advanced vocabulary)", 'normal'),
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
        $this->setSessionValue(self::CHAIN_KEY, []);
        $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);
        $this->setSessionValue(self::GAME_OVER_KEY, false);
        $this->setSessionValue(self::USED_WORDS_KEY, []);

        $this->setCurrentStep(self::STEP_PLAY);

        return $this->interactiveOutput([
            $this->formatOutput("Difficulty: " . ucfirst($difficulty), 'info'),
            "",
            $this->formatOutput("Let's start! Enter your first word:", 'warning'),
        ]);
    }

    protected function handlePlayStep(string $input): array
    {
        // Check if game is over
        if ($this->getSessionValue(self::GAME_OVER_KEY, false)) {
            return $this->handlePlayAgainStep($input);
        }

        $chain = $this->getSessionValue(self::CHAIN_KEY);
        $currentPlayer = $this->getSessionValue(self::CURRENT_PLAYER_KEY);
        $usedWords = $this->getSessionValue(self::USED_WORDS_KEY);
        $difficulty = $this->getSessionValue(self::DIFFICULTY_KEY);

        // Check if chain has reached maximum length
        $maxChainLength = match ($difficulty) {
            self::DIFFICULTY_EASY => self::MAX_CHAIN_LENGTH_EASY,
            self::DIFFICULTY_MEDIUM => self::MAX_CHAIN_LENGTH_MEDIUM,
            self::DIFFICULTY_HARD => self::MAX_CHAIN_LENGTH_HARD,
        };

        if (count($chain) >= $maxChainLength) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);
            return $this->interactiveOutput([
                $this->formatChain($chain),
                "",
                $this->formatOutput("Game Over! You've reached the maximum chain length of {$maxChainLength} words!", 'success'),
                $this->formatOutput("That's an impressive chain!", 'info'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
            ]);
        }

        // Human player's turn
        if ($currentPlayer === self::PLAYER_HUMAN) {
            // Validate input
            if (strlen($input) < 3) {
                return $this->interactiveOutput([
                    $this->formatOutput("Word must be at least 3 letters long!", 'error'),
                    $this->formatChain($chain),
                    "",
                    $this->formatOutput("Enter your word:", 'warning'),
                ]);
            }

            // Check if word was already used
            if (in_array(strtolower($input), $usedWords)) {
                return $this->interactiveOutput([
                    $this->formatOutput("Word already used! Try another word:", 'error'),
                    $this->formatChain($chain),
                    "",
                    $this->formatOutput("Enter your word:", 'warning'),
                ]);
            }

            // Check if word follows the chain rule
            if (!empty($chain) && strtolower($input[0]) !== strtolower($chain[count($chain) - 1][-1])) {
                return $this->interactiveOutput([
                    $this->formatOutput("Word must start with '" . strtoupper($chain[count($chain) - 1][-1]) . "'!", 'error'),
                    $this->formatChain($chain),
                    "",
                    $this->formatOutput("Enter your word:", 'warning'),
                ]);
            }

            // Check if word exists in dictionary
            $firstLetter = strtolower($input[0]);
            if (!isset($this->dictionary[$firstLetter]) || !in_array(strtolower($input), $this->dictionary[$firstLetter])) {
                return $this->interactiveOutput([
                    $this->formatOutput("That's not a valid word! Try another word:", 'error'),
                    $this->formatChain($chain),
                    "",
                    $this->formatOutput("Enter your word:", 'warning'),
                ]);
            }

            // Add word to chain
            $chain[] = $input;
            $usedWords[] = strtolower($input);
            $this->setSessionValue(self::CHAIN_KEY, $chain);
            $this->setSessionValue(self::USED_WORDS_KEY, $usedWords);

            // Switch to computer's turn
            $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_COMPUTER);

            // Make computer move
            $computerWord = $this->getComputerWord($chain[count($chain) - 1][-1], $usedWords);
            if ($computerWord === null) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);
                $message = count($chain) >= self::MIN_WORDS_FOR_VICTORY
                ? "Computer couldn't find a word! You win with a chain of " . count($chain) . " words!"
                : "Game Over! The chain ended after " . count($chain) . " words.";
                return $this->interactiveOutput([
                    $this->formatChain($chain),
                    "",
                    $this->formatOutput($message, 'success'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            $chain[] = $computerWord;
            $usedWords[] = strtolower($computerWord);
            $this->setSessionValue(self::CHAIN_KEY, $chain);
            $this->setSessionValue(self::USED_WORDS_KEY, $usedWords);

            // Switch back to human's turn
            $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);

            return $this->interactiveOutput([
                $this->formatChain($chain),
                "",
                $this->formatOutput("Computer played: " . $computerWord, 'info'),
                $this->formatOutput("Chain length: " . count($chain) . " words", 'info'),
                "",
                $this->formatOutput("Your turn! Enter a word starting with '" . strtoupper($computerWord[-1]) . "':", 'warning'),
            ]);
        }

        return $this->interactiveOutput([
            $this->formatOutput("Error: Invalid game state", 'error'),
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
                $this->formatOutput("1. Easy (Common words)", 'normal'),
                $this->formatOutput("2. Medium (Wider vocabulary)", 'normal'),
                $this->formatOutput("3. Hard (Advanced vocabulary)", 'normal'),
                "",
                $this->formatOutput("Enter your choice (1-3):", 'warning'),
            ]);
        } else {
            // End the game
            $this->clearSession();

            return [
                $this->formatOutput("Thanks for playing!", 'success'),
                $this->formatOutput("Run 'wordchain' to play again.", 'info'),
            ];
        }
    }

    protected function formatChain(array $chain): string
    {
        if (empty($chain)) {
            return "Chain: (empty)";
        }

        return "Chain: " . implode(" -> ", $chain);
    }

    protected function getComputerWord(string $lastLetter, array $usedWords): ?string
    {
        $this->loadDictionary();
        $lastLetter = strtolower($lastLetter);
        if (!isset($this->dictionary[$lastLetter])) {
            return null;
        }

        $difficulty = $this->getSessionValue(self::DIFFICULTY_KEY);
        $maxLength = match ($difficulty) {
            self::DIFFICULTY_EASY => self::MAX_WORD_LENGTH_EASY,
            self::DIFFICULTY_MEDIUM => self::MAX_WORD_LENGTH_MEDIUM,
            self::DIFFICULTY_HARD => self::MAX_WORD_LENGTH_HARD,
        };

        // Filter words by length and exclude used words
        $availableWords = array_filter(
            $this->dictionary[$lastLetter],
            fn($word) => strlen($word) <= $maxLength && !in_array($word, $usedWords)
        );

        if (empty($availableWords)) {
            return null;
        }

        // For hard difficulty, try to find words that end with uncommon letters
        if ($difficulty === self::DIFFICULTY_HARD) {
            $uncommonEndings = ['q', 'x', 'z', 'j', 'v'];
            $hardWords = array_filter(
                $availableWords,
                fn($word) => in_array($word[-1], $uncommonEndings)
            );
            if (!empty($hardWords)) {
                return $hardWords[array_rand($hardWords)];
            }
        }

        return $availableWords[array_rand($availableWords)];
    }
}
