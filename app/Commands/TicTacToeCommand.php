<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;

class TicTacToeCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'tictactoe';
    protected $description = 'Play Tic Tac Toe Game against Computer';
    protected $aliases = ['ttt', 'tictactoe'];
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
            $this->formatOutput("Tic Tac Toe Game vs Computer", 'header'),
            $this->formatOutput("=========================", 'subheader'),
            "",
            $this->formatOutput("Choose difficulty level:", 'info'),
            $this->formatOutput("1. Easy (Random moves)", 'normal'),
            $this->formatOutput("2. Medium (Basic strategy)", 'normal'),
            $this->formatOutput("3. Hard (Unbeatable)", 'normal'),
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
        $board = array_fill(0, 9, self::EMPTY_CELL);
        $this->setSessionValue(self::BOARD_KEY, $board);
        $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);
        $this->setSessionValue(self::GAME_OVER_KEY, false);
        $this->setSessionValue(self::WINNER_KEY, null);

        $this->setCurrentStep(self::STEP_PLAY);

        return $this->interactiveOutput([
            $this->formatOutput("Difficulty: " . ucfirst($difficulty), 'info'),
            "",
            $this->formatOutput("Board positions are numbered 1-9:", 'info'),
            $this->formatOutput("    1 | 2 | 3", 'normal'),
            $this->formatOutput("   -----------", 'normal'),
            $this->formatOutput("    4 | 5 | 6", 'normal'),
            $this->formatOutput("   -----------", 'normal'),
            $this->formatOutput("    7 | 8 | 9", 'normal'),
            "",
            $this->formatOutput("Current board:", 'info'),
            $this->formatBoard($board),
            "",
            $this->formatOutput("Your turn (X)! Enter position (1-9):", 'warning'),
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
            if (!is_numeric($input) || $input < 1 || $input > 9) {
                return $this->interactiveOutput([
                    $this->formatOutput("Invalid input! Please enter a number between 1 and 9:", 'error'),
                    $this->formatOutput("Current board:", 'info'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Your turn (X)! Enter position (1-9):", 'warning'),
                ]);
            }

            $position = (int) $input - 1; // Convert to 0-based index

            // Check if position is already taken
            if ($board[$position] !== self::EMPTY_CELL) {
                return $this->interactiveOutput([
                    $this->formatOutput("That position is already taken! Try another position:", 'error'),
                    $this->formatOutput("Current board:", 'info'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Your turn (X)! Enter position (1-9):", 'warning'),
                ]);
            }

            // Make human move
            $board[$position] = self::PLAYER_HUMAN;
            $this->setSessionValue(self::BOARD_KEY, $board);

            // Check for human win
            if ($this->checkWin($board, self::PLAYER_HUMAN)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);
                $this->setSessionValue(self::WINNER_KEY, self::PLAYER_HUMAN);

                return $this->interactiveOutput([
                    $this->formatOutput("Current board:", 'info'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Congratulations! You win!", 'success'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Check for draw
            if ($this->isBoardFull($board)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);

                return $this->interactiveOutput([
                    $this->formatOutput("Current board:", 'info'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("It's a draw!", 'info'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Switch to computer's turn
            $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_COMPUTER);

            // Make computer move
            $computerMove = $this->makeComputerMove($board);
            $board[$computerMove] = self::PLAYER_COMPUTER;
            $this->setSessionValue(self::BOARD_KEY, $board);

            // Check for computer win
            if ($this->checkWin($board, self::PLAYER_COMPUTER)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);
                $this->setSessionValue(self::WINNER_KEY, self::PLAYER_COMPUTER);

                return $this->interactiveOutput([
                    $this->formatOutput("Current board:", 'info'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("Computer wins!", 'error'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Check for draw after computer move
            if ($this->isBoardFull($board)) {
                $this->setSessionValue(self::GAME_OVER_KEY, true);

                return $this->interactiveOutput([
                    $this->formatOutput("Current board:", 'info'),
                    $this->formatBoard($board),
                    "",
                    $this->formatOutput("It's a draw!", 'info'),
                    "",
                    $this->formatOutput("Want to play again? (yes/no):", 'warning'),
                ]);
            }

            // Switch back to human's turn
            $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);

            return $this->interactiveOutput([
                $this->formatOutput("Current board:", 'info'),
                $this->formatBoard($board),
                "",
                $this->formatOutput("Your turn (X)! Enter position (1-9):", 'warning'),
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
                $this->formatOutput("3. Hard (Unbeatable)", 'normal'),
                "",
                $this->formatOutput("Enter your choice (1-3):", 'warning'),
            ]);
        } else {
            // End the game
            $this->clearSession();

            return [
                $this->formatOutput("Thanks for playing!", 'success'),
                $this->formatOutput("Run 'tictactoe' to play again.", 'info'),
            ];
        }
    }

    protected function formatBoard(array $board): string
    {
        return implode("\n", [
            $this->formatOutput("    ╔═══╦═══╦═══╗", 'normal'),
            $this->formatOutput("    ║ {$board[0]} ║ {$board[1]} ║ {$board[2]} ║", 'normal'),
            $this->formatOutput("    ╠═══╬═══╬═══╣", 'normal'),
            $this->formatOutput("    ║ {$board[3]} ║ {$board[4]} ║ {$board[5]} ║", 'normal'),
            $this->formatOutput("    ╠═══╬═══╬═══╣", 'normal'),
            $this->formatOutput("    ║ {$board[6]} ║ {$board[7]} ║ {$board[8]} ║", 'normal'),
            $this->formatOutput("    ╚═══╩═══╩═══╝", 'normal'),
        ]);
    }

    protected function checkWin(array $board, string $player): bool
    {
        // Winning combinations
        $winningCombos = [
            [0, 1, 2], // Top row
            [3, 4, 5], // Middle row
            [6, 7, 8], // Bottom row
            [0, 3, 6], // Left column
            [1, 4, 7], // Middle column
            [2, 5, 8], // Right column
            [0, 4, 8], // Diagonal
            [2, 4, 6], // Diagonal
        ];

        foreach ($winningCombos as $combo) {
            if ($board[$combo[0]] === $player &&
                $board[$combo[1]] === $player &&
                $board[$combo[2]] === $player) {
                return true;
            }
        }

        return false;
    }

    protected function isBoardFull(array $board): bool
    {
        return !in_array(self::EMPTY_CELL, $board);
    }

    protected function makeComputerMove(array $board): int
    {
        $difficulty = $this->getSessionValue(self::DIFFICULTY_KEY);

        return match ($difficulty) {
            self::DIFFICULTY_EASY => $this->makeEasyMove($board),
            self::DIFFICULTY_MEDIUM => $this->makeMediumMove($board),
            self::DIFFICULTY_HARD => $this->makeHardMove($board),
            default => $this->makeEasyMove($board),
        };
    }

    protected function makeEasyMove(array $board): int
    {
        // Get all empty positions
        $emptyPositions = array_keys(array_filter($board, fn($cell) => $cell === self::EMPTY_CELL));

        // Randomly select an empty position
        return $emptyPositions[array_rand($emptyPositions)];
    }

    protected function makeMediumMove(array $board): int
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

        // Try to take center
        if ($board[4] === self::EMPTY_CELL) {
            return 4;
        }

        // Try to take corners
        $corners = [0, 2, 6, 8];
        $emptyCorners = array_filter($corners, fn($pos) => $board[$pos] === self::EMPTY_CELL);
        if (!empty($emptyCorners)) {
            return $emptyCorners[array_rand($emptyCorners)];
        }

        // Take any available space
        return $this->makeEasyMove($board);
    }

    protected function makeHardMove(array $board): int
    {
        $bestScore = -INF;
        $bestMove = null;

        // Try each empty position
        for ($i = 0; $i < 9; $i++) {
            if ($board[$i] === self::EMPTY_CELL) {
                $board[$i] = self::PLAYER_COMPUTER;
                $score = $this->minimax($board, 0, false);
                $board[$i] = self::EMPTY_CELL;

                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMove = $i;
                }
            }
        }

        return $bestMove;
    }

    protected function minimax(array $board, int $depth, bool $isMaximizing): int
    {
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

        if ($isMaximizing) {
            $bestScore = -INF;
            for ($i = 0; $i < 9; $i++) {
                if ($board[$i] === self::EMPTY_CELL) {
                    $board[$i] = self::PLAYER_COMPUTER;
                    $score = $this->minimax($board, $depth + 1, false);
                    $board[$i] = self::EMPTY_CELL;
                    $bestScore = max($score, $bestScore);
                }
            }
            return $bestScore;
        } else {
            $bestScore = INF;
            for ($i = 0; $i < 9; $i++) {
                if ($board[$i] === self::EMPTY_CELL) {
                    $board[$i] = self::PLAYER_HUMAN;
                    $score = $this->minimax($board, $depth + 1, true);
                    $board[$i] = self::EMPTY_CELL;
                    $bestScore = min($score, $bestScore);
                }
            }
            return $bestScore;
        }
    }

    protected function findWinningMove(array $board, string $player): ?int
    {
        // Try each empty position
        for ($i = 0; $i < 9; $i++) {
            if ($board[$i] === self::EMPTY_CELL) {
                $board[$i] = $player;
                if ($this->checkWin($board, $player)) {
                    return $i;
                }
                $board[$i] = self::EMPTY_CELL;
            }
        }
        return null;
    }
}
