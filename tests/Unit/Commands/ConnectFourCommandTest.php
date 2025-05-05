<?php

namespace Tests\Unit\Commands;

use App\Commands\ConnectFourCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\TerminalTestTrait;

class ConnectFourCommandTest extends TestCase
{
    use TerminalTestTrait;

    protected $command;
    protected $terminal;
    protected $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new ConnectFourCommand();
        $this->terminal = $this->initializeTerminal();
        $this->reflection = new ReflectionClass(ConnectFourCommand::class);
        $this->clearGameState();
    }

    protected function tearDown(): void
    {
        $this->clearGameState();
        parent::tearDown();
    }

    protected function clearGameState(): void
    {
        Session::forget([
            $this->getSessionKey($this->getConstant('BOARD_KEY')),
            $this->getSessionKey($this->getConstant('CURRENT_PLAYER_KEY')),
            $this->getSessionKey($this->getConstant('GAME_OVER_KEY')),
            $this->getSessionKey($this->getConstant('WINNER_KEY')),
            $this->getSessionKey($this->getConstant('DIFFICULTY_KEY')),
        ]);
        Session::forget($this->getStepKey());
    }

    protected function getConstant(string $name)
    {
        return $this->reflection->getConstant($name);
    }

    protected function getSessionKey(string $key): string
    {
        return sprintf('%s_%s', 'connectfour', $key);
    }

    protected function getStepKey(): string
    {
        return sprintf('%s_step', 'connectfour');
    }

    protected function setupGameState(array $state = []): void
    {
        $defaults = [
            'step' => $this->getConstant('STEP_DIFFICULTY'),
            'difficulty' => 'easy',
            'current_player' => 'X',
            'board' => array_fill(0, 6, array_fill(0, 7, ' ')),
            'game_over' => false,
            'winner' => null,
        ];

        $state = array_merge($defaults, $state);

        Session::put($this->getStepKey(), $state['step']);
        Session::put($this->getSessionKey($this->getConstant('DIFFICULTY_KEY')), $state['difficulty']);
        Session::put($this->getSessionKey($this->getConstant('CURRENT_PLAYER_KEY')), $state['current_player']);
        Session::put($this->getSessionKey($this->getConstant('BOARD_KEY')), $state['board']);
        Session::put($this->getSessionKey($this->getConstant('GAME_OVER_KEY')), $state['game_over']);
        Session::put($this->getSessionKey($this->getConstant('WINNER_KEY')), $state['winner']);
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('connectfour', $this->command->getName());
        $this->assertEquals('Play Connect Four against Computer', $this->command->getDescription());
    }

    public function test_command_has_correct_aliases()
    {
        $aliases = $this->command->getAliases();
        $this->assertContains('connect4', $aliases);
        $this->assertContains('c4', $aliases);
    }

    public function test_command_is_hidden()
    {
        $this->assertTrue($this->command->isHidden());
    }

    public function test_command_starts_with_difficulty_selection()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Connect Four Game vs Computer', $output[0]);
        $this->assertStringContainsString('Choose difficulty level:', $output[3]);
        $this->assertStringContainsString('Enter your choice (1-3):', $output[count($output) - 1]);
    }

    public function test_command_handles_invalid_difficulty_selection()
    {
        // First execute to get to difficulty selection
        $this->command->execute($this->terminal);

        // Try invalid selection
        $output = $this->command->execute($this->terminal, ['4']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Invalid choice! Please enter 1, 2, or 3:', $output[0]);
    }

    public function test_command_starts_game_after_valid_difficulty_selection()
    {
        // Set up difficulty selection step
        $this->setupGameState(['step' => $this->getConstant('STEP_DIFFICULTY')]);

        // Select difficulty
        $output = $this->command->execute($this->terminal, ['1']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Difficulty: Easy', $output[0]);
        $this->assertStringContainsString('Your turn (X)! Enter column (1-7):', $output[count($output) - 1]);
    }

    public function test_command_handles_invalid_column_selection()
    {
        // Set up play step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'difficulty' => 'easy',
            'current_player' => 'X',
        ]);

        // Try invalid column
        $output = $this->command->execute($this->terminal, ['8']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Invalid input! Please enter a number between 1 and 7:', $output[0]);
    }

    public function test_command_handles_full_column()
    {
        // Set up play step with a full column
        $board = array_fill(0, 6, array_fill(0, 7, ' '));
        for ($i = 0; $i < 6; $i++) {
            $board[$i][0] = 'X';
        }

        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'difficulty' => 'easy',
            'current_player' => 'X',
            'board' => $board,
        ]);

        // Try full column
        $output = $this->command->execute($this->terminal, ['1']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('That column is full! Try another column:', $output[0]);
    }

    public function test_command_detects_horizontal_win()
    {
        // Set up play step with almost winning board
        $board = array_fill(0, 6, array_fill(0, 7, ' '));
        $board[5][0] = 'X';
        $board[5][1] = 'X';
        $board[5][2] = 'X';

        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'difficulty' => 'easy',
            'current_player' => 'X',
            'board' => $board,
        ]);

        // Make winning move
        $output = $this->command->execute($this->terminal, ['4']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Congratulations! You win!', $output[2]);
    }

    public function test_command_detects_vertical_win()
    {
        // Set up play step with almost winning board
        $board = array_fill(0, 6, array_fill(0, 7, ' '));
        $board[5][0] = 'X';
        $board[4][0] = 'X';
        $board[3][0] = 'X';

        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'difficulty' => 'easy',
            'current_player' => 'X',
            'board' => $board,
        ]);

        // Make winning move
        $output = $this->command->execute($this->terminal, ['1']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Congratulations! You win!', $output[2]);
    }

    public function test_command_detects_diagonal_win()
    {
        // Set up play step with almost winning board
        $board = array_fill(0, 6, array_fill(0, 7, ' '));

        // Set up diagonal pattern from bottom up
        $board[5][0] = 'X'; // Bottom piece
        $board[5][1] = 'O'; // Opponent's blocking piece
        $board[4][1] = 'X'; // Second piece
        $board[5][2] = 'O'; // Opponent's blocking piece
        $board[4][2] = 'O'; // Opponent's blocking piece
        $board[3][2] = 'X'; // Third piece
        $board[5][3] = 'O'; // Opponent's blocking piece
        $board[4][3] = 'O'; // Opponent's blocking piece
        $board[3][3] = 'O'; // Opponent's blocking piece
        $board[2][3] = ' '; // Empty space for winning move

        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'difficulty' => 'easy',
            'current_player' => 'X',
            'board' => $board,
        ]);

        // Make winning move in column 4 (index 3)
        $output = $this->command->execute($this->terminal, ['4']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Congratulations! You win!', $output[2]);
    }

    public function test_command_detects_draw()
    {
        // Set up play step with almost full board
        $board = array_fill(0, 6, array_fill(0, 7, ' '));

        // Fill the board in a way that prevents any wins
        // Pattern: XOXOXOX
        //         OXOXOXO
        //         XOXOXOX
        //         OXOXOXO
        //         XOXOXOX
        //         OXOXOX  (last space empty)

        // Fill the board with alternating pieces in a zigzag pattern
        $isX = true;
        for ($col = 0; $col < 7; $col++) {
            $startWithX = $col % 2 === 0;
            $isX = $startWithX;

            $startRow = 5;
            $endRow = ($col === 6) ? 1 : 0; // Leave last space empty in last column

            for ($row = $startRow; $row >= $endRow; $row--) {
                $board[$row][$col] = $isX ? 'X' : 'O';
                $isX = !$isX;
            }
        }

        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'difficulty' => 'easy',
            'current_player' => 'X',
            'board' => $board,
        ]);

        // Debug: Print board state
        echo "\nBoard state before move:\n";
        for ($row = 0; $row < 6; $row++) {
            echo implode(' ', $board[$row]) . "\n";
        }

        // Make final move
        $output = $this->command->execute($this->terminal, ['7']);

        // Debug: Print output
        echo "\nCommand output:\n";
        foreach ($output as $line) {
            echo $line . "\n";
        }

        $this->assertNotEmpty($output);
        foreach ($output as $line) {
            if (str_contains($line, "It's a draw!")) {
                $this->assertTrue(true);
                return;
            }
        }
        $this->fail("Draw message not found in output");
    }

    public function test_command_handles_play_again()
    {
        // Set up play again step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY_AGAIN'),
            'difficulty' => 'easy',
            'current_player' => 'X',
            'game_over' => true,
        ]);

        $output = $this->command->execute($this->terminal, ['no']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Thanks for playing!', $output[0]);
    }
}
