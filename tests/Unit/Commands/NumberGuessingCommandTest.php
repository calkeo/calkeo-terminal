<?php

namespace Tests\Unit\Commands;

use App\Commands\NumberGuessingCommand;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class NumberGuessingCommandTest extends TestCase
{
    protected $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new NumberGuessingCommand();
    }

    public function test_command_starts_interactive_process()
    {
        // Clear any existing session data
        Session::forget([
            'numberguess_step',
            'numberguess_target_number',
            'numberguess_attempts',
            'numberguess_min_number',
            'numberguess_max_number',
            'numberguess_game_over',
        ]);

        $output = $this->command->execute();

        // Check that we have the expected header
        $this->assertStringContainsString('Number Guessing Game', $output[0]);

        // Check that we have the expected options
        $this->assertStringContainsString('1. Easy (1-50, 10 attempts)', $output[4]);
        $this->assertStringContainsString('2. Medium (1-100, 7 attempts)', $output[5]);
        $this->assertStringContainsString('3. Hard (1-200, 5 attempts)', $output[6]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Enter your choice (1-3):', $output[8]);

        // Check that the command is interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_invalid_difficulty_choice()
    {
        // Set the current step to DIFFICULTY
        Session::put('numberguess_step', 1);

        $output = $this->command->execute(['invalid']);

        // Check that we have the expected error message
        $this->assertStringContainsString('Invalid choice! Please enter 1, 2, or 3:', $output[0]);

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_valid_difficulty_choice()
    {
        // Set the current step to DIFFICULTY
        Session::put('numberguess_step', 1);

        $output = $this->command->execute(['1']);

        // Check that we have the expected output
        $this->assertStringContainsString('Difficulty: Easy', $output[0]);
        $this->assertStringContainsString('Range: 1 to 50', $output[1]);
        $this->assertStringContainsString('Attempts remaining: 10', $output[2]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Enter your guess:', implode(' ', $output));

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_invalid_guess()
    {
        // Set the current step to GUESS
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute(['invalid']);

        // Check that we have the expected error message
        $this->assertStringContainsString('Invalid input! Please enter a number:', $output[0]);

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_out_of_range_guess()
    {
        // Set the current step to GUESS
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute(['100']);

        // Check that we have the expected error message
        $this->assertStringContainsString('Your guess must be between 1 and 50!', $output[0]);

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_correct_guess()
    {
        // Set the current step to GUESS
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute(['25']);

        // Check that we have the expected output
        $this->assertStringContainsString('Congratulations! You\'ve guessed the number 25!', $output[0]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Want to play again? (yes/no):', $output[2]);

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_incorrect_guess()
    {
        // Set the current step to GUESS
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 10);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute(['10']);

        // Check that we have the expected output
        $this->assertStringContainsString('Your guess is too low!', $output[0]);
        $this->assertStringContainsString('Attempts remaining: 9', $output[1]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Enter your guess:', $output[3]);

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_out_of_attempts()
    {
        // Set the current step to GUESS
        Session::put('numberguess_step', 2);
        Session::put('numberguess_min_number', 1);
        Session::put('numberguess_max_number', 50);
        Session::put('numberguess_attempts', 1);
        Session::put('numberguess_target_number', 25);
        Session::put('numberguess_game_over', false);

        $output = $this->command->execute(['10']);

        // Check that we have the expected output
        $this->assertStringContainsString('Game Over! You\'ve run out of attempts.', $output[0]);
        $this->assertStringContainsString('The number was 25.', $output[1]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Want to play again? (yes/no):', $output[3]);

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_play_again_yes()
    {
        // Set the current step to PLAY_AGAIN
        Session::put('numberguess_step', 3);
        Session::put('numberguess_game_over', true);

        $output = $this->command->execute(['yes']);

        // Check that we have the expected output
        $this->assertStringContainsString("Great! Let's play again!", $output[0]);

        // Check that we have the expected options
        $this->assertStringContainsString('Choose difficulty level:', $output[2]);
        $this->assertStringContainsString('1. Easy (1-50, 10 attempts)', $output[3]);
        $this->assertStringContainsString('2. Medium (1-100, 7 attempts)', $output[4]);
        $this->assertStringContainsString('3. Hard (1-200, 5 attempts)', $output[5]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Enter your choice (1-3):', $output[7]);

        // Check that the command is still interactive
        $this->assertContains('__INTERACTIVE__', $output);
    }

    public function test_command_handles_play_again_no()
    {
        // Set the current step to PLAY_AGAIN
        Session::put('numberguess_step', 3);
        Session::put('numberguess_game_over', true);

        $output = $this->command->execute(['no']);

        // Check that we have the expected output
        $this->assertStringContainsString('Thanks for playing!', $output[0]);
        $this->assertStringContainsString("Run 'numberguess' to play again.", $output[1]);

        // Check that the command is no longer interactive
        $this->assertNotContains('__INTERACTIVE__', $output);
    }
}
