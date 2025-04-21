<?php

namespace Tests\Unit\Commands;

use App\Commands\NumberGuessingCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class NumberGuessingCommandTest extends TestCase
{
    protected $command;
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new NumberGuessingCommand();
        $this->terminal = new Terminal();
        Session::forget([
            'numberguess_step',
            'numberguess_target_number',
            'numberguess_attempts',
            'numberguess_min_number',
            'numberguess_max_number',
            'numberguess_game_over',
        ]);
    }

    public function test_command_starts_interactive_process()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Number Guessing Game', $output[0]);
        $this->assertStringContainsString('Choose difficulty level:', $output[3]);
        $this->assertEquals(1, Session::get('numberguess_step'));
    }

    public function test_command_handles_invalid_difficulty_choice()
    {
        Session::put('numberguess_step', 1);

        $output = $this->command->execute($this->terminal, ['invalid']);

        $this->assertStringContainsString('Invalid choice! Please enter 1, 2, or 3:', $output[0]);
        $this->assertEquals(1, Session::get('numberguess_step'));
    }

    public function test_command_handles_valid_difficulty_choice()
    {
        Session::put('numberguess_step', 1);

        $output = $this->command->execute($this->terminal, ['1']);

        $this->assertStringContainsString('Difficulty: Easy', $output[0]);
        $this->assertStringContainsString('Range: 1 to 50', $output[1]);
        $this->assertStringContainsString('Attempts remaining: 10', $output[2]);
        $this->assertEquals(2, Session::get('numberguess_step'));
        $this->assertNotNull(Session::get('numberguess_target_number'));
        $this->assertEquals(10, Session::get('numberguess_attempts'));
    }

    public function test_command_handles_invalid_guess()
    {
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute($this->terminal, ['not a number']);

        $this->assertStringContainsString('Invalid input! Please enter a number:', $output[0]);
        $this->assertEquals(2, Session::get('numberguess_step'));
        $this->assertEquals(10, Session::get('numberguess_attempts'));
    }

    public function test_command_handles_out_of_range_guess()
    {
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute($this->terminal, ['100']);

        $this->assertStringContainsString('Your guess must be between 1 and 50!', $output[0]);
        $this->assertEquals(2, Session::get('numberguess_step'));
        $this->assertEquals(10, Session::get('numberguess_attempts'));
    }

    public function test_command_handles_correct_guess()
    {
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute($this->terminal, ['25']);

        $this->assertStringContainsString('Congratulations!', $output[0]);
        $this->assertEquals(2, Session::get('numberguess_step'));
        $this->assertTrue(Session::get('numberguess_game_over'));
    }

    public function test_command_handles_incorrect_guess()
    {
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute($this->terminal, ['10']);

        $this->assertStringContainsString('Your guess is too low!', $output[0]);
        $this->assertEquals(2, Session::get('numberguess_step'));
        $this->assertEquals(9, Session::get('numberguess_attempts'));
    }

    public function test_command_handles_out_of_attempts()
    {
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 1);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute($this->terminal, ['10']);

        $this->assertStringContainsString('Game Over!', $output[0]);
        $this->assertEquals(2, Session::get('numberguess_step'));
        $this->assertTrue(Session::get('numberguess_game_over'));
    }

    public function test_command_handles_play_again_yes()
    {
        Session::put('numberguess_step', 2);
        Session::put('numberguess_game_over', true);

        $output = $this->command->execute($this->terminal, ['yes']);

        $this->assertStringContainsString('Choose difficulty level:', $output[2]);
        $this->assertEquals(1, Session::get('numberguess_step'));
    }

    public function test_command_handles_play_again_no()
    {
        Session::put('numberguess_step', 2);
        Session::put('numberguess_game_over', true);

        $output = $this->command->execute($this->terminal, ['no']);

        $this->assertStringContainsString('Thanks for playing!', $output[0]);
        $this->assertNull(Session::get('numberguess_step'));
    }
}
