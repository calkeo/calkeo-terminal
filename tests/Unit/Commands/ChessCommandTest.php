<?php

namespace Tests\Unit\Commands;

use App\Commands\ChessCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\TerminalTestTrait;

class ChessCommandTest extends TestCase
{
    use TerminalTestTrait;

    protected $command;
    protected $terminal;
    protected $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new ChessCommand();
        $this->terminal = $this->initializeTerminal();
        $this->reflection = new ReflectionClass(ChessCommand::class);
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
            $this->getSessionKey($this->getConstant('SELECTED_PIECE_KEY')),
            $this->getSessionKey($this->getConstant('VALID_MOVES_KEY')),
            $this->getSessionKey($this->getConstant('LAST_MOVE_KEY')),
        ]);
        Session::forget($this->getStepKey());
    }

    protected function getConstant(string $name)
    {
        return $this->reflection->getConstant($name);
    }

    protected function getSessionKey(string $key): string
    {
        return sprintf('%s_%s', 'chess', $key);
    }

    protected function getStepKey(): string
    {
        return sprintf('%s_step', 'chess');
    }

    protected function setupGameState(array $state = []): void
    {
        $defaults = [
            'step' => $this->getConstant('STEP_DIFFICULTY'),
            'difficulty' => 'easy',
            'current_player' => 'white',
            'board' => array_fill(0, 8, array_fill(0, 8, null)),
            'game_over' => false,
            'winner' => null,
            'selected_piece' => null,
            'valid_moves' => [],
            'last_move' => null,
        ];

        $state = array_merge($defaults, $state);

        Session::put($this->getStepKey(), $state['step']);
        Session::put($this->getSessionKey($this->getConstant('DIFFICULTY_KEY')), $state['difficulty']);
        Session::put($this->getSessionKey($this->getConstant('CURRENT_PLAYER_KEY')), $state['current_player']);
        Session::put($this->getSessionKey($this->getConstant('BOARD_KEY')), $state['board']);
        Session::put($this->getSessionKey($this->getConstant('GAME_OVER_KEY')), $state['game_over']);
        Session::put($this->getSessionKey($this->getConstant('WINNER_KEY')), $state['winner']);
        Session::put($this->getSessionKey($this->getConstant('SELECTED_PIECE_KEY')), $state['selected_piece']);
        Session::put($this->getSessionKey($this->getConstant('VALID_MOVES_KEY')), $state['valid_moves']);
        Session::put($this->getSessionKey($this->getConstant('LAST_MOVE_KEY')), $state['last_move']);
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('chess', $this->command->getName());
        $this->assertEquals('Play Chess against Computer', $this->command->getDescription());
    }

    public function test_command_has_correct_aliases()
    {
        $aliases = $this->command->getAliases();
        $this->assertContains('chess', $aliases);
    }

    public function test_command_is_hidden()
    {
        $this->assertTrue($this->command->isHidden());
    }

    public function test_command_starts_with_difficulty_selection()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Chess Game vs Computer', $output[0]);
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
        $this->assertStringContainsString('Your turn (White)! Select a piece', $output[count($output) - 1]);
    }

    public function test_command_validates_piece_selection()
    {
        // Set up play step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_SELECT_PIECE'),
            'difficulty' => 'easy',
            'current_player' => 'white',
        ]);

        // Try invalid square
        $output = $this->command->execute($this->terminal, ['z9']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Invalid input! Please enter a valid square', $output[0]);
    }

    public function test_command_validates_piece_ownership()
    {
        // Set up play step with a black piece at a1
        $board = array_fill(0, 8, array_fill(0, 8, null));
        $board[0][0] = ['color' => 'black', 'type' => 'rook'];

        $this->setupGameState([
            'step' => $this->getConstant('STEP_SELECT_PIECE'),
            'difficulty' => 'easy',
            'current_player' => 'white',
            'board' => $board,
        ]);

        // Try selecting black piece
        $output = $this->command->execute($this->terminal, ['a1']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('No piece at that square! Try another square:', $output[0]);
    }

    public function test_command_handles_game_over()
    {
        // Set up play again step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY_AGAIN'),
            'difficulty' => 'easy',
            'current_player' => 'white',
            'game_over' => true,
        ]);

        $output = $this->command->execute($this->terminal, ['no']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Thanks for playing!', $output[0]);
    }
}
