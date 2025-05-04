<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;

class ChessCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'chess';
    protected $description = 'Play Chess against Computer';
    protected $aliases = ['chess'];
    protected $hidden = true;

    // Session keys
    protected const BOARD_KEY = 'board';
    protected const CURRENT_PLAYER_KEY = 'current_player';
    protected const GAME_OVER_KEY = 'game_over';
    protected const WINNER_KEY = 'winner';
    protected const DIFFICULTY_KEY = 'difficulty';
    protected const SELECTED_PIECE_KEY = 'selected_piece';
    protected const VALID_MOVES_KEY = 'valid_moves';
    protected const LAST_MOVE_KEY = 'last_move';

    // Step definitions
    protected const STEP_START = 1;
    protected const STEP_DIFFICULTY = 2;
    protected const STEP_SELECT_PIECE = 3;
    protected const STEP_MOVE_PIECE = 4;
    protected const STEP_PLAY_AGAIN = 5;

    // Players
    protected const PLAYER_HUMAN = 'white';
    protected const PLAYER_COMPUTER = 'black';

    // Difficulty levels
    protected const DIFFICULTY_EASY = 'easy';
    protected const DIFFICULTY_MEDIUM = 'medium';
    protected const DIFFICULTY_HARD = 'hard';

    // Chess pieces
    protected const PIECES = [
        'white' => [
            'king' => '♔',
            'queen' => '♕',
            'rook' => '♖',
            'bishop' => '♗',
            'knight' => '♘',
            'pawn' => '♙',
        ],
        'black' => [
            'king' => '♚',
            'queen' => '♛',
            'rook' => '♜',
            'bishop' => '♝',
            'knight' => '♞',
            'pawn' => '♟',
        ],
    ];

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
            self::SELECTED_PIECE_KEY,
            self::VALID_MOVES_KEY,
            self::LAST_MOVE_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_DIFFICULTY);

        return $this->interactiveOutput([
            $this->formatOutput("Chess Game vs Computer", 'header'),
            $this->formatOutput("====================", 'subheader'),
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
            case self::STEP_SELECT_PIECE:
                $this->terminal->replaceLastOutput();
                return $this->handleSelectPieceStep($input);
            case self::STEP_MOVE_PIECE:
                $this->terminal->replaceLastOutput();
                return $this->handleMovePieceStep($input);
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
        $board = $this->initializeBoard();
        $this->setSessionValue(self::BOARD_KEY, $board);
        $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);
        $this->setSessionValue(self::GAME_OVER_KEY, false);
        $this->setSessionValue(self::WINNER_KEY, null);
        $this->setSessionValue(self::SELECTED_PIECE_KEY, null);
        $this->setSessionValue(self::VALID_MOVES_KEY, []);
        $this->setSessionValue(self::LAST_MOVE_KEY, null);

        $this->setCurrentStep(self::STEP_SELECT_PIECE);

        return $this->interactiveOutput([
            $this->formatOutput("Difficulty: " . ucfirst($difficulty), 'info'),
            "",
            $this->formatOutput("Current board:", 'info'),
            $this->formatBoard($board),
            "",
            $this->formatOutput("Your turn (White)! Select a piece (e.g., 'e2'):", 'warning'),
        ]);
    }

    protected function handleSelectPieceStep(string $input): array
    {
        // Check if game is over
        if ($this->getSessionValue(self::GAME_OVER_KEY, false)) {
            return $this->handlePlayAgainStep($input);
        }

        $board = $this->getSessionValue(self::BOARD_KEY);
        $currentPlayer = $this->getSessionValue(self::CURRENT_PLAYER_KEY);

        // Validate input format
        if (!preg_match('/^[a-h][1-8]$/', $input)) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid input! Please enter a valid square (e.g., 'e2'):", 'error'),
                $this->formatBoard($board),
                "",
                $this->formatOutput("Your turn (White)! Select a piece:", 'warning'),
            ]);
        }

        // Convert input to coordinates
        $x = ord($input[0]) - ord('a');
        $y = 8 - intval($input[1]);

        // Check if square contains a piece
        if (!isset($board[$y][$x]) || $board[$y][$x] === null) {
            return $this->interactiveOutput([
                $this->formatOutput("No piece at that square! Try another square:", 'error'),
                $this->formatBoard($board),
                "",
                $this->formatOutput("Your turn (White)! Select a piece:", 'warning'),
            ]);
        }

        // Check if piece belongs to current player
        $piece = $board[$y][$x];
        if ($piece['color'] !== $currentPlayer) {
            return $this->interactiveOutput([
                $this->formatOutput("That's not your piece! Try another square:", 'error'),
                $this->formatBoard($board),
                "",
                $this->formatOutput("Your turn (White)! Select a piece:", 'warning'),
            ]);
        }

        // Get valid moves for the piece
        $validMoves = $this->getValidMoves($board, $x, $y);
        if (empty($validMoves)) {
            return $this->interactiveOutput([
                $this->formatOutput("That piece has no valid moves! Try another piece:", 'error'),
                $this->formatBoard($board),
                "",
                $this->formatOutput("Your turn (White)! Select a piece:", 'warning'),
            ]);
        }

        // Store selected piece and valid moves
        $this->setSessionValue(self::SELECTED_PIECE_KEY, ['x' => $x, 'y' => $y]);
        $this->setSessionValue(self::VALID_MOVES_KEY, $validMoves);

        // Move to move piece step
        $this->setCurrentStep(self::STEP_MOVE_PIECE);

        return $this->interactiveOutput([
            $this->formatBoard($board, $validMoves),
            "",
            $this->formatOutput("Selected piece at {$input}. Enter destination (e.g., 'e4'):", 'warning'),
        ]);
    }

    protected function handleMovePieceStep(string $input): array
    {
        $board = $this->getSessionValue(self::BOARD_KEY);
        $selectedPiece = $this->getSessionValue(self::SELECTED_PIECE_KEY);
        $validMoves = $this->getSessionValue(self::VALID_MOVES_KEY);

        // Validate input format
        if (!preg_match('/^[a-h][1-8]$/', $input)) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid input! Please enter a valid square (e.g., 'e4'):", 'error'),
                $this->formatBoard($board, $validMoves),
                "",
                $this->formatOutput("Enter destination for selected piece:", 'warning'),
            ]);
        }

        // Convert input to coordinates
        $destX = ord($input[0]) - ord('a');
        $destY = 8 - intval($input[1]);

        // Check if move is valid
        $isValidMove = false;
        foreach ($validMoves as $move) {
            if ($move['x'] === $destX && $move['y'] === $destY) {
                $isValidMove = true;
                break;
            }
        }

        if (!$isValidMove) {
            return $this->interactiveOutput([
                $this->formatOutput("Invalid move! Try another destination:", 'error'),
                $this->formatBoard($board, $validMoves),
                "",
                $this->formatOutput("Enter destination for selected piece:", 'warning'),
            ]);
        }

        // Make the move
        $piece = $board[$selectedPiece['y']][$selectedPiece['x']];
        $board[$destY][$destX] = $piece;
        $board[$selectedPiece['y']][$selectedPiece['x']] = null;

        // Store the move
        $this->setSessionValue(self::LAST_MOVE_KEY, [
            'from' => $selectedPiece,
            'to' => ['x' => $destX, 'y' => $destY],
        ]);

        // Check for game over conditions
        if ($this->isCheckmate($board, self::PLAYER_COMPUTER)) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);
            $this->setSessionValue(self::WINNER_KEY, self::PLAYER_HUMAN);
            $this->setCurrentStep(self::STEP_PLAY_AGAIN);

            return $this->interactiveOutput([
                $this->formatBoard($board),
                "",
                $this->formatOutput("Checkmate! You win!", 'success'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
            ]);
        }

        // Computer's turn
        $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_COMPUTER);
        $this->setSessionValue(self::BOARD_KEY, $board);
        $this->setSessionValue(self::SELECTED_PIECE_KEY, null);
        $this->setSessionValue(self::VALID_MOVES_KEY, []);

        // Make computer move
        $computerMove = $this->makeComputerMove($board);
        if ($computerMove === null) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);
            $this->setSessionValue(self::WINNER_KEY, self::PLAYER_HUMAN);
            $this->setCurrentStep(self::STEP_PLAY_AGAIN);

            return $this->interactiveOutput([
                $this->formatBoard($board),
                "",
                $this->formatOutput("Computer has no valid moves! You win!", 'success'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
            ]);
        }

        // Update board with computer's move
        $board = $computerMove['board'];
        $this->setSessionValue(self::BOARD_KEY, $board);
        $this->setSessionValue(self::LAST_MOVE_KEY, [
            'from' => $computerMove['from'],
            'to' => $computerMove['to'],
        ]);

        // Check for game over conditions after computer's move
        if ($this->isCheckmate($board, self::PLAYER_HUMAN)) {
            $this->setSessionValue(self::GAME_OVER_KEY, true);
            $this->setSessionValue(self::WINNER_KEY, self::PLAYER_COMPUTER);
            $this->setCurrentStep(self::STEP_PLAY_AGAIN);

            return $this->interactiveOutput([
                $this->formatBoard($board),
                "",
                $this->formatOutput("Checkmate! Computer wins!", 'error'),
                "",
                $this->formatOutput("Want to play again? (yes/no):", 'warning'),
            ]);
        }

        // Switch back to human's turn
        $this->setSessionValue(self::CURRENT_PLAYER_KEY, self::PLAYER_HUMAN);
        $this->setCurrentStep(self::STEP_SELECT_PIECE);

        return $this->interactiveOutput([
            $this->formatBoard($board),
            "",
            $this->formatOutput("Computer moved from " . $this->formatSquare($computerMove['from']) .
                " to " . $this->formatSquare($computerMove['to']), 'info'),
            "",
            $this->formatOutput("Your turn (White)! Select a piece:", 'warning'),
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
                $this->formatOutput("Run 'chess' to play again.", 'info'),
            ];
        }
    }

    protected function initializeBoard(): array
    {
        $board = array_fill(0, 8, array_fill(0, 8, null));

        // Set up white pieces
        $board[7][0] = ['color' => 'white', 'type' => 'rook'];
        $board[7][1] = ['color' => 'white', 'type' => 'knight'];
        $board[7][2] = ['color' => 'white', 'type' => 'bishop'];
        $board[7][3] = ['color' => 'white', 'type' => 'queen'];
        $board[7][4] = ['color' => 'white', 'type' => 'king'];
        $board[7][5] = ['color' => 'white', 'type' => 'bishop'];
        $board[7][6] = ['color' => 'white', 'type' => 'knight'];
        $board[7][7] = ['color' => 'white', 'type' => 'rook'];
        for ($i = 0; $i < 8; $i++) {
            $board[6][$i] = ['color' => 'white', 'type' => 'pawn'];
        }

        // Set up black pieces
        $board[0][0] = ['color' => 'black', 'type' => 'rook'];
        $board[0][1] = ['color' => 'black', 'type' => 'knight'];
        $board[0][2] = ['color' => 'black', 'type' => 'bishop'];
        $board[0][3] = ['color' => 'black', 'type' => 'queen'];
        $board[0][4] = ['color' => 'black', 'type' => 'king'];
        $board[0][5] = ['color' => 'black', 'type' => 'bishop'];
        $board[0][6] = ['color' => 'black', 'type' => 'knight'];
        $board[0][7] = ['color' => 'black', 'type' => 'rook'];
        for ($i = 0; $i < 8; $i++) {
            $board[1][$i] = ['color' => 'black', 'type' => 'pawn'];
        }

        return $board;
    }

    protected function formatBoard(array $board, array $validMoves = []): string
    {
        $output = [];
        $output[] = "<div class=\"chess-board\">";
        $output[] = "<div class=\"chess-labels\">";
        $output[] = "<div class=\"chess-label\"></div>";
        for ($x = 0; $x < 8; $x++) {
            $output[] = "<div class=\"chess-label\">" . chr(97 + $x) . "</div>";
        }
        $output[] = "<div class=\"chess-label\"></div>";
        $output[] = "</div>";

        for ($y = 0; $y < 8; $y++) {
            $output[] = "<div class=\"chess-row\">";
            $output[] = "<div class=\"chess-label\">" . (8 - $y) . "</div>";

            for ($x = 0; $x < 8; $x++) {
                $isValidMove = false;
                foreach ($validMoves as $move) {
                    if ($move['x'] === $x && $move['y'] === $y) {
                        $isValidMove = true;
                        break;
                    }
                }

                $cell = $board[$y][$x];
                $isWhite = ($x + $y) % 2 === 0;
                $cellClass = $isWhite ? 'chess-cell white' : 'chess-cell black';
                if ($isValidMove) {
                    $cellClass .= ' valid-move';
                }

                $output[] = "<div class=\"{$cellClass}\">";
                if ($cell !== null) {
                    $piece = self::PIECES[$cell['color']][$cell['type']];
                    $pieceClass = $cell['color'] === 'white' ? 'chess-piece white' : 'chess-piece black';
                    $output[] = "<span class=\"{$pieceClass}\">{$piece}</span>";
                } else if ($isValidMove) {
                    $output[] = "<span class=\"valid-move-dot\">·</span>";
                }
                $output[] = "</div>";
            }

            $output[] = "<div class=\"chess-label\">" . (8 - $y) . "</div>";
            $output[] = "</div>";
        }

        $output[] = "<div class=\"chess-labels\">";
        $output[] = "<div class=\"chess-label\"></div>";
        for ($x = 0; $x < 8; $x++) {
            $output[] = "<div class=\"chess-label\">" . chr(97 + $x) . "</div>";
        }
        $output[] = "<div class=\"chess-label\"></div>";
        $output[] = "</div>";
        $output[] = "</div>";

        return implode("\n", $output);
    }

    protected function formatOutput(string $text, string $type = 'normal'): string
    {
        $colors = [
            'header' => 'text-cyan-500 font-bold',
            'subheader' => 'text-purple-500 font-bold',
            'info' => 'text-blue-500 font-bold',
            'warning' => 'text-yellow-500 font-bold',
            'error' => 'text-red-500 font-bold',
            'success' => 'text-green-500 font-bold',
            'white' => 'text-white font-bold',
            'normal' => 'text-gray-200',
        ];

        return "<span class=\"{$colors[$type]}\">{$text}</span>";
    }

    protected function getValidMoves(array $board, int $x, int $y): array
    {
        $piece = $board[$y][$x];
        if ($piece === null) {
            return [];
        }

        $validMoves = [];
        switch ($piece['type']) {
            case 'pawn':
                $validMoves = $this->getPawnMoves($board, $x, $y, $piece['color']);
                break;
            case 'rook':
                $validMoves = $this->getRookMoves($board, $x, $y, $piece['color']);
                break;
            case 'knight':
                $validMoves = $this->getKnightMoves($board, $x, $y, $piece['color']);
                break;
            case 'bishop':
                $validMoves = $this->getBishopMoves($board, $x, $y, $piece['color']);
                break;
            case 'queen':
                $validMoves = $this->getQueenMoves($board, $x, $y, $piece['color']);
                break;
            case 'king':
                $validMoves = $this->getKingMoves($board, $x, $y, $piece['color']);
                break;
        }

        return $validMoves;
    }

    protected function getPawnMoves(array $board, int $x, int $y, string $color): array
    {
        $moves = [];
        $direction = $color === 'white' ? -1 : 1;
        $startRow = $color === 'white' ? 6 : 1;

        // Move forward one square
        if ($y + $direction >= 0 && $y + $direction < 8 && $board[$y + $direction][$x] === null) {
            $moves[] = ['x' => $x, 'y' => $y + $direction];
        }

        // Move forward two squares from starting position
        if ($y === $startRow && $board[$y + $direction][$x] === null && $board[$y + 2 * $direction][$x] === null) {
            $moves[] = ['x' => $x, 'y' => $y + 2 * $direction];
        }

        // Capture diagonally
        foreach ([-1, 1] as $dx) {
            $newX = $x + $dx;
            $newY = $y + $direction;
            if ($newX >= 0 && $newX < 8 && $newY >= 0 && $newY < 8) {
                $target = $board[$newY][$newX];
                if ($target !== null && $target['color'] !== $color) {
                    $moves[] = ['x' => $newX, 'y' => $newY];
                }
            }
        }

        return $moves;
    }

    protected function getRookMoves(array $board, int $x, int $y, string $color): array
    {
        $moves = [];
        $directions = [[0, 1], [0, -1], [1, 0], [-1, 0]];

        foreach ($directions as $dir) {
            $newX = $x + $dir[0];
            $newY = $y + $dir[1];
            while ($newX >= 0 && $newX < 8 && $newY >= 0 && $newY < 8) {
                $target = $board[$newY][$newX];
                if ($target === null) {
                    $moves[] = ['x' => $newX, 'y' => $newY];
                } else {
                    if ($target['color'] !== $color) {
                        $moves[] = ['x' => $newX, 'y' => $newY];
                    }
                    break;
                }
                $newX += $dir[0];
                $newY += $dir[1];
            }
        }

        return $moves;
    }

    protected function getKnightMoves(array $board, int $x, int $y, string $color): array
    {
        $moves = [];
        $knightMoves = [
            [-2, -1], [-2, 1], [-1, -2], [-1, 2],
            [1, -2], [1, 2], [2, -1], [2, 1],
        ];

        foreach ($knightMoves as $move) {
            $newX = $x + $move[0];
            $newY = $y + $move[1];
            if ($newX >= 0 && $newX < 8 && $newY >= 0 && $newY < 8) {
                $target = $board[$newY][$newX];
                if ($target === null || $target['color'] !== $color) {
                    $moves[] = ['x' => $newX, 'y' => $newY];
                }
            }
        }

        return $moves;
    }

    protected function getBishopMoves(array $board, int $x, int $y, string $color): array
    {
        $moves = [];
        $directions = [[1, 1], [1, -1], [-1, 1], [-1, -1]];

        foreach ($directions as $dir) {
            $newX = $x + $dir[0];
            $newY = $y + $dir[1];
            while ($newX >= 0 && $newX < 8 && $newY >= 0 && $newY < 8) {
                $target = $board[$newY][$newX];
                if ($target === null) {
                    $moves[] = ['x' => $newX, 'y' => $newY];
                } else {
                    if ($target['color'] !== $color) {
                        $moves[] = ['x' => $newX, 'y' => $newY];
                    }
                    break;
                }
                $newX += $dir[0];
                $newY += $dir[1];
            }
        }

        return $moves;
    }

    protected function getQueenMoves(array $board, int $x, int $y, string $color): array
    {
        return array_merge(
            $this->getRookMoves($board, $x, $y, $color),
            $this->getBishopMoves($board, $x, $y, $color)
        );
    }

    protected function getKingMoves(array $board, int $x, int $y, string $color): array
    {
        $moves = [];
        $kingMoves = [
            [-1, -1], [-1, 0], [-1, 1],
            [0, -1], [0, 1],
            [1, -1], [1, 0], [1, 1],
        ];

        foreach ($kingMoves as $move) {
            $newX = $x + $move[0];
            $newY = $y + $move[1];
            if ($newX >= 0 && $newX < 8 && $newY >= 0 && $newY < 8) {
                $target = $board[$newY][$newX];
                if ($target === null || $target['color'] !== $color) {
                    $moves[] = ['x' => $newX, 'y' => $newY];
                }
            }
        }

        return $moves;
    }

    protected function isCheckmate(array $board, string $color): bool
    {
        // Find the king
        $kingX = null;
        $kingY = null;
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $piece = $board[$y][$x];
                if ($piece !== null && $piece['type'] === 'king' && $piece['color'] === $color) {
                    $kingX = $x;
                    $kingY = $y;
                    break 2;
                }
            }
        }

        if ($kingX === null || $kingY === null) {
            return true; // King not found, game is over
        }

        // Check if king is in check
        $opponentColor = $color === 'white' ? 'black' : 'white';
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $piece = $board[$y][$x];
                if ($piece !== null && $piece['color'] === $opponentColor) {
                    $moves = $this->getValidMoves($board, $x, $y);
                    foreach ($moves as $move) {
                        if ($move['x'] === $kingX && $move['y'] === $kingY) {
                            return true; // King is in check
                        }
                    }
                }
            }
        }

        return false;
    }

    protected function makeComputerMove(array $board): ?array
    {
        $difficulty = $this->getSessionValue(self::DIFFICULTY_KEY);

        return match ($difficulty) {
            self::DIFFICULTY_EASY => $this->makeEasyMove($board),
            self::DIFFICULTY_MEDIUM => $this->makeMediumMove($board),
            self::DIFFICULTY_HARD => $this->makeHardMove($board),
            default => $this->makeEasyMove($board),
        };
    }

    protected function makeEasyMove(array $board): ?array
    {
        // Get all computer pieces
        $pieces = [];
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $piece = $board[$y][$x];
                if ($piece !== null && $piece['color'] === self::PLAYER_COMPUTER) {
                    $pieces[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        // Try each piece until we find a valid move
        shuffle($pieces);
        foreach ($pieces as $piece) {
            $moves = $this->getValidMoves($board, $piece['x'], $piece['y']);
            if (!empty($moves)) {
                $move = $moves[array_rand($moves)];
                $newBoard = $board;
                $newBoard[$move['y']][$move['x']] = $board[$piece['y']][$piece['x']];
                $newBoard[$piece['y']][$piece['x']] = null;
                return [
                    'board' => $newBoard,
                    'from' => $piece,
                    'to' => $move,
                ];
            }
        }

        return null;
    }

    protected function makeMediumMove(array $board): ?array
    {
        // First, try to capture a piece
        $captureMove = $this->findCaptureMove($board);
        if ($captureMove !== null) {
            return $captureMove;
        }

        // Then, try to move a piece to a better position
        $positionMove = $this->findPositionMove($board);
        if ($positionMove !== null) {
            return $positionMove;
        }

        // If no good moves found, make a random move
        return $this->makeEasyMove($board);
    }

    protected function makeHardMove(array $board): ?array
    {
        // First, try to find a checkmate move
        $checkmateMove = $this->findCheckmateMove($board);
        if ($checkmateMove !== null) {
            return $checkmateMove;
        }

        // Then, try to capture a piece
        $captureMove = $this->findCaptureMove($board);
        if ($captureMove !== null) {
            return $captureMove;
        }

        // Then, try to move a piece to a better position
        $positionMove = $this->findPositionMove($board);
        if ($positionMove !== null) {
            return $positionMove;
        }

        // If no good moves found, make a random move
        return $this->makeEasyMove($board);
    }

    protected function findCaptureMove(array $board): ?array
    {
        $pieces = [];
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $piece = $board[$y][$x];
                if ($piece !== null && $piece['color'] === self::PLAYER_COMPUTER) {
                    $pieces[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        foreach ($pieces as $piece) {
            $moves = $this->getValidMoves($board, $piece['x'], $piece['y']);
            foreach ($moves as $move) {
                $target = $board[$move['y']][$move['x']];
                if ($target !== null && $target['color'] === self::PLAYER_HUMAN) {
                    $newBoard = $board;
                    $newBoard[$move['y']][$move['x']] = $board[$piece['y']][$piece['x']];
                    $newBoard[$piece['y']][$piece['x']] = null;
                    return [
                        'board' => $newBoard,
                        'from' => $piece,
                        'to' => $move,
                    ];
                }
            }
        }

        return null;
    }

    protected function findPositionMove(array $board): ?array
    {
        $pieces = [];
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $piece = $board[$y][$x];
                if ($piece !== null && $piece['color'] === self::PLAYER_COMPUTER) {
                    $pieces[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        // Try to move pieces toward the center
        $centerMoves = [];
        foreach ($pieces as $piece) {
            $moves = $this->getValidMoves($board, $piece['x'], $piece['y']);
            foreach ($moves as $move) {
                $distanceToCenter = abs($move['x'] - 3.5) + abs($move['y'] - 3.5);
                $currentDistance = abs($piece['x'] - 3.5) + abs($piece['y'] - 3.5);
                if ($distanceToCenter < $currentDistance) {
                    $centerMoves[] = [
                        'move' => $move,
                        'piece' => $piece,
                        'score' => $currentDistance - $distanceToCenter,
                    ];
                }
            }
        }

        if (!empty($centerMoves)) {
            usort($centerMoves, fn($a, $b) => $b['score'] <=> $a['score']);
            $bestMove = $centerMoves[0];
            $newBoard = $board;
            $newBoard[$bestMove['move']['y']][$bestMove['move']['x']] = $board[$bestMove['piece']['y']][$bestMove['piece']['x']];
            $newBoard[$bestMove['piece']['y']][$bestMove['piece']['x']] = null;
            return [
                'board' => $newBoard,
                'from' => $bestMove['piece'],
                'to' => $bestMove['move'],
            ];
        }

        return null;
    }

    protected function findCheckmateMove(array $board): ?array
    {
        $pieces = [];
        for ($y = 0; $y < 8; $y++) {
            for ($x = 0; $x < 8; $x++) {
                $piece = $board[$y][$x];
                if ($piece !== null && $piece['color'] === self::PLAYER_COMPUTER) {
                    $pieces[] = ['x' => $x, 'y' => $y];
                }
            }
        }

        foreach ($pieces as $piece) {
            $moves = $this->getValidMoves($board, $piece['x'], $piece['y']);
            foreach ($moves as $move) {
                $newBoard = $board;
                $newBoard[$move['y']][$move['x']] = $board[$piece['y']][$piece['x']];
                $newBoard[$piece['y']][$piece['x']] = null;
                if ($this->isCheckmate($newBoard, self::PLAYER_HUMAN)) {
                    return [
                        'board' => $newBoard,
                        'from' => $piece,
                        'to' => $move,
                    ];
                }
            }
        }

        return null;
    }

    protected function formatSquare(array $square): string
    {
        return chr($square['x'] + ord('a')) . (8 - $square['y']);
    }
}
