<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;

class ConnectFourCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'connectfour';
    protected $description = 'Play Connect Four against Computer';
    protected $aliases = ['connect4', 'c4'];
    protected $hidden = true;

    // Session keys
    protected const BOARD_KEY = 'board';
    protected const CURRENT_PLAYER_KEY = 'current_player';
    protected const GAME_OVER_KEY = 'game_over';
    protected const WINNER_KEY = 'winner';
    protected const DIFFICULTY_KEY = 'difficulty';

    // Step definitions
    protected const STEP_START = 1;
    protected const STEP_DIFFICULTY = 2;
    protected const STEP_PLAY = 3;
    protected const STEP_PLAY_AGAIN = 4;

    // Players
    protected const PLAYER_HUMAN = 'X';
    protected const PLAYER_COMPUTER = 'O';
    protected const EMPTY_CELL = ' ';

    // Difficulty levels
    protected const DIFFICULTY_EASY = 'easy';
    protected const DIFFICULTY_MEDIUM = 'medium';
    protected const DIFFICULTY_HARD = 'hard';

    // Board dimensions
    protected const ROWS = 6;
    protected const COLS = 7;

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
            self::BOARD_KEY,
            self::CURRENT_PLAYER_KEY,
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
            $this->formatOutput("Connect Four Game vs Computer", 'header'),
            $this->formatOutput("===========================", 'subheader'),
            "",
            $this->formatOutput("Choose difficulty level:", 'info'),
            $this->formatOutput("1. Easy (Random moves)", 'normal'),
            $this->formatOutput("2. Medium (Basic strategy)", 'normal'),
            $this->formatOutput("3. Hard (Advanced strategy)", 'normal'),
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
        $board = array_fill(0, self::ROWS, array_fill(0, self::COLS, self::EMPTY_CELL));
        $this->setSessionValue(self::BOARD_KEY, $board);
        $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);
        $this->setSessionValue(self::GAME_OVER_KEY, false);
        $this->setSessionValue(self::WINNER_KEY, null);

        $this->setCurrentStep(self::STEP_PLAY);

        return $this->interactiveOutput([
            $this->formatOutput("Difficulty: " . ucfirst($difficulty), 'info'),
            "",
            $this->formatOutput("Current board:", 'info'),
            $this->formatBoard($board),
            "",
            $this->formatOutput("Your turn (X)! Enter column (1-7):", 'warning'),
        ]);
    }

    protected function handlePlayStep(string $input): array
    {
        // Check if game is over
        if ($this->getSessionValue(self::GAME_OVER_KEY, false)) {
            return $this->handlePlayAgainStep($input);
        }

        $board = $this->getSessionValue(self::BOARD_KEY);
        $currentPlayer = $this->getSessionValue(self::CURRENT_PLAYER_KEY);

        // Human player's turn
        if ($currentPlayer === self::PLAYER_HUMAN) {
            // Validate input
            if (!is_numeric($input) || $input < 1 || $input > self::COLS) {
                return $this->interactiveOutput([
                    $this->formatOutput("Invalid input! Please enter a number between 1 and " . self::COLS . ":", 'error'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Your turn (X)! Enter column (1-7):", 'warning'),
                ]);
            }

            $col = (int) $input - 1; // Convert to 0-based index

            // Check if column is full
            if ($board[0][$col] !== self::EMPTY_CELL) {
                return $this->interactiveOutput([
                    $this->formatOutput("That column is full! Try another column:", 'error'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Your turn (X)! Enter column (1-7):", 'warning'),
                ]);
            }

            // Find the lowest empty row in the column
            $row = $this->findLowestEmptyRow($board, $col);

            // Make human move
            $board[$row][$col] = self::PLAYER_HUMAN;
            $this->setSessionValue(self::BOARD_KEY, $board);

            // Check for draw first
            if ($this->isBoardFull($board)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);

                return $this->interactiveOutput([
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("It's a draw!", 'info'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Then check for human win
            if ($this->checkWin($board, self::PLAYER_HUMAN)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);
                $this->setSessionValue(self::WINNER_KEY, self::PLAYER_HUMAN);

                return $this->interactiveOutput([
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Congratulations! You win!", 'success'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Switch to computer's turn
            $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_COMPUTER);

            // Make computer move
            $computerMove = $this->makeComputerMove($board);
            $board[$computerMove['row']][$computerMove['col']] = self::PLAYER_COMPUTER;
            $this->setSessionValue(self::BOARD_KEY, $board);

            // Check for draw after computer move
            if ($this->isBoardFull($board)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);

                return $this->interactiveOutput([
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("It's a draw!", 'info'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Then check for computer win
            if ($this->checkWin($board, self::PLAYER_COMPUTER)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);
                $this->setSessionValue(self::WINNER_KEY, self::PLAYER_COMPUTER);

                return $this->interactiveOutput([
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Computer wins!", 'error'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Switch back to human's turn
            $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);

            return $this->interactiveOutput([
                $this->formatBoard($board),
                "",
                $this->formatOutput("Your turn (X)! Enter column (1-7):", 'warning'),
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
                $this->formatOutput("1. Easy (Random moves)", 'normal'),
                $this->formatOutput("2. Medium (Basic strategy)", 'normal'),
                $this->formatOutput("3. Hard (Advanced strategy)", 'normal'),
                "",
                $this->formatOutput("Enter your choice (1-3):", 'warning'),
            ]);
        } else {
            // End the game
            $this->clearSession();

            return [
                $this->formatOutput("Thanks for playing!", 'success'),
                $this->formatOutput("Run 'connectfour' to play again.", 'info'),
            ];
        }
    }

    protected function formatBoard(array $board): string
    {
        $output = [];
        $output[] = $this->formatOutput("    1   2   3   4   5   6   7", 'info');
        $output[] = $this->formatOutput("  ╔═══╦═══╦═══╦═══╦═══╦═══╦═══╗", 'warning');

        for ($row = 0; $row < self::ROWS; $row++) {
            $line = "  ║";
            for ($col = 0; $col < self::COLS; $col++) {
                $cell = $board[$row][$col];
                $line .= " " . $this->formatCell($cell) . " ║";
            }
            $output[] = $line;

            if ($row < self::ROWS - 1) {
                $output[] = $this->formatOutput("  ╠═══╬═══╬═══╬═══╬═══╬═══╬═══╣", 'warning');
            }
        }

        $output[] = $this->formatOutput("  ╚═══╩═══╩═══╩═══╩═══╩═══╩═══╝", 'warning');
        $output[] = $this->formatOutput("    1   2   3   4   5   6   7", 'info');

        return implode("\n", $output);
    }

    protected function formatCell(string $cell): string
    {
        if ($cell === self::PLAYER_HUMAN) {
            return $this->formatOutput($cell, 'error');
        } elseif ($cell === self::PLAYER_COMPUTER) {
            return $this->formatOutput($cell, 'success');
        }
        return $cell;
    }

    protected function findLowestEmptyRow(array $board, int $col): int
    {
        for ($row = self::ROWS - 1; $row >= 0; $row--) {
            if ($board[$row][$col] === self::EMPTY_CELL) {
                return $row;
            }
        }
        return -1; // Column is full
    }

    protected function checkWin(array $board, string $player): bool
    {
        // Check horizontal
        for ($row = 0; $row < self::ROWS; $row++) {
            for ($col = 0; $col < self::COLS - 3; $col++) {
                if ($board[$row][$col] === $player &&
                    $board[$row][$col + 1] === $player &&
                    $board[$row][$col + 2] === $player &&
                    $board[$row][$col + 3] === $player) {
                    return true;
                }
            }
        }

        // Check vertical
        for ($row = 0; $row < self::ROWS - 3; $row++) {
            for ($col = 0; $col < self::COLS; $col++) {
                if ($board[$row][$col] === $player &&
                    $board[$row + 1][$col] === $player &&
                    $board[$row + 2][$col] === $player &&
                    $board[$row + 3][$col] === $player) {
                    return true;
                }
            }
        }

        // Check diagonal (down-right)
        for ($row = 0; $row < self::ROWS - 3; $row++) {
            for ($col = 0; $col < self::COLS - 3; $col++) {
                if ($board[$row][$col] === $player &&
                    $board[$row + 1][$col + 1] === $player &&
                    $board[$row + 2][$col + 2] === $player &&
                    $board[$row + 3][$col + 3] === $player) {
                    return true;
                }
            }
        }

        // Check diagonal (down-left)
        for ($row = 0; $row < self::ROWS - 3; $row++) {
            for ($col = 3; $col < self::COLS; $col++) {
                if ($board[$row][$col] === $player &&
                    $board[$row + 1][$col - 1] === $player &&
                    $board[$row + 2][$col - 2] === $player &&
                    $board[$row + 3][$col - 3] === $player) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function isBoardFull(array $board): bool
    {
        for ($col = 0; $col < self::COLS; $col++) {
            if ($board[0][$col] === self::EMPTY_CELL) {
                return false;
            }
        }
        return true;
    }

    protected function makeComputerMove(array $board): array
    {
        $difficulty = $this->getSessionValue(self::DIFFICULTY_KEY);

        return match ($difficulty) {
            self::DIFFICULTY_EASY => $this->makeEasyMove($board),
            self::DIFFICULTY_MEDIUM => $this->makeMediumMove($board),
            self::DIFFICULTY_HARD => $this->makeHardMove($board),
            default => $this->makeEasyMove($board),
        };
    }

    protected function makeEasyMove(array $board): array
    {
        // Get all available columns
        $availableCols = [];
        for ($col = 0; $col < self::COLS; $col++) {
            if ($board[0][$col] === self::EMPTY_CELL) {
                $availableCols[] = $col;
            }
        }

        // Randomly select a column
        $col = $availableCols[array_rand($availableCols)];
        $row = $this->findLowestEmptyRow($board, $col);

        return ['row' => $row, 'col' => $col];
    }

    protected function makeMediumMove(array $board): array
    {
        // First, check if computer can win
        $winningMove = $this->findWinningMove($board, self::PLAYER_COMPUTER);
        if ($winningMove !== null) {
            return $winningMove;
        }

        // Then, check if need to block player
        $blockingMove = $this->findWinningMove($board, self::PLAYER_HUMAN);
        if ($blockingMove !== null) {
            return $blockingMove;
        }

        // Try to take center column
        $centerCol = (int) (self::COLS / 2);
        if ($board[0][$centerCol] === self::EMPTY_CELL) {
            return [
                'row' => $this->findLowestEmptyRow($board, $centerCol),
                'col' => $centerCol,
            ];
        }

        // Take any available space
        return $this->makeEasyMove($board);
    }

    protected function makeHardMove(array $board): array
    {
        $bestScore = -INF;
        $bestMove = null;

        // Try each available column
        for ($col = 0; $col < self::COLS; $col++) {
            if ($board[0][$col] === self::EMPTY_CELL) {
                $row = $this->findLowestEmptyRow($board, $col);
                $board[$row][$col] = self::PLAYER_COMPUTER;
                $score = $this->minimax($board, 0, false);
                $board[$row][$col] = self::EMPTY_CELL;

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMove = ['row' => $row, 'col' => $col];
                }
            }
        }

        // If no move was found (shouldn't happen in normal gameplay), fall back to medium difficulty
        if ($bestMove === null) {
            return $this->makeMediumMove($board);
        }

        return $bestMove;
    }

    protected function minimax(array $board, int $depth, bool $isMaximizing): int
    {
        // Limit search depth to make the AI respond faster
        if ($depth > 5) {
            return 0;
        }

        // Check for terminal states
        if ($this->checkWin($board, self::PLAYER_COMPUTER)) {
            return 10 - $depth;
        }
        if ($this->checkWin($board, self::PLAYER_HUMAN)) {
            return -10 + $depth;
        }
        if ($this->isBoardFull($board)) {
            return 0;
        }

        // Create a copy of the board to avoid modifying the original
        $boardCopy = array_map(function ($row) {
            return array_map(function ($cell) {
                return $cell;
            }, $row);
        }, $board);

        if ($isMaximizing) {
            $bestScore = -INF;
            for ($col = 0; $col < self::COLS; $col++) {
                if ($boardCopy[0][$col] === self::EMPTY_CELL) {
                    $row = $this->findLowestEmptyRow($boardCopy, $col);
                    $boardCopy[$row][$col] = self::PLAYER_COMPUTER;
                    $score = $this->minimax($boardCopy, $depth + 1, false);
                    $boardCopy[$row][$col] = self::EMPTY_CELL;
                    $bestScore = max($score, $bestScore);
                }
            }
            return $bestScore;
        } else {
            $bestScore = INF;
            for ($col = 0; $col < self::COLS; $col++) {
                if ($boardCopy[0][$col] === self::EMPTY_CELL) {
                    $row = $this->findLowestEmptyRow($boardCopy, $col);
                    $boardCopy[$row][$col] = self::PLAYER_HUMAN;
                    $score = $this->minimax($boardCopy, $depth + 1, true);
                    $boardCopy[$row][$col] = self::EMPTY_CELL;
                    $bestScore = min($score, $bestScore);
                }
            }
            return $bestScore;
        }
    }

    protected function findWinningMove(array $board, string $player): ?array
    {
        // Try each column
        for ($col = 0; $col < self::COLS; $col++) {
            if ($board[0][$col] === self::EMPTY_CELL) {
                $row = $this->findLowestEmptyRow($board, $col);
                $board[$row][$col] = $player;
                if ($this->checkWin($board, $player)) {
                    return ['row' => $row, 'col' => $col];
                }
                $board[$row][$col] = self::EMPTY_CELL;
            }
        }
        return null;
    }
}
