<?php

namespace Tests\Unit\Commands;

use App\Commands\CommandStates;
use App\Commands\RockPaperScissorsCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use Nette\Utils\ReflectionMethod;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\TerminalTestTrait;

class RockPaperScissorsCommandTest extends TestCase
{
    use TerminalTestTrait;

    protected $command;
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new RockPaperScissorsCommand();
        $this->terminal = $this->initializeTerminal();
    }

    public function test_command_starts_interactive_process()
    {
        // Clear any existing session data
        Session::forget(['rps_step', 'rps_choice', 'rps_computer_choice', 'rps_result']);

        $output = $this->command->execute($this->terminal);

        // Check that we have the expected header
        $this->assertStringContainsString('Rock, Paper, Scissors', $output[0]);

        // Check that we have the expected options
        $this->assertStringContainsString('1. Rock 🪨', $output[4]);
        $this->assertStringContainsString('2. Paper 📄', $output[5]);
        $this->assertStringContainsString('3. Scissors ✂️', $output[6]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Enter your choice (1-3):', $output[8]);

        // Check that the command is interactive
        $this->assertTrue($this->terminal->getCommandState()->has(CommandStates::INTERACTIVE));
    }

    public function test_command_handles_invalid_choice()
    {
        // Set the current step to CHOICE
        Session::put('rps_step', 1);

        $output = $this->command->execute($this->terminal, ['invalid']);

        // Check that we have the expected error message
        $this->assertStringContainsString('Invalid choice! Please enter 1, 2, or 3:', $output[0]);

        // Check that the command is still interactive
        $this->assertTrue($this->terminal->getCommandState()->has(CommandStates::INTERACTIVE));
    }

    public function test_command_handles_valid_choice()
    {
        // Set the current step to CHOICE
        Session::put('rps_step', 1);

        $output = $this->command->execute($this->terminal, ['1']);

        // Check that we have the expected output
        $this->assertStringContainsString('You chose: Rock 🪨', $output[0]);
        $this->assertStringContainsString('Computer chose:', $output[1]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Want to play again? (yes/no):', $output[5]);

        // Check that the command is still interactive
        $this->assertTrue($this->terminal->getCommandState()->has(CommandStates::INTERACTIVE));
    }

    public function test_command_handles_play_again_yes()
    {
        // Set the current step to RESULT
        Session::put('rps_step', 2);

        $output = $this->command->execute($this->terminal, ['yes']);

        // Check that we have the expected output
        $this->assertStringContainsString("Great! Let's play again!", $output[0]);

        // Check that we have the expected options
        $this->assertStringContainsString('Make your choice:', $output[2]);
        $this->assertStringContainsString('1. Rock 🪨', $output[3]);
        $this->assertStringContainsString('2. Paper 📄', $output[4]);
        $this->assertStringContainsString('3. Scissors ✂️', $output[5]);

        // Check that we have the expected prompt
        $this->assertStringContainsString('Enter your choice (1-3):', $output[7]);

        // Check that the command is still interactive
        $this->assertTrue($this->terminal->getCommandState()->has(CommandStates::INTERACTIVE));
    }

    public function test_command_handles_play_again_no()
    {
        // Set the current step to RESULT
        Session::put('rps_step', 2);

        $output = $this->command->execute($this->terminal, ['no']);

        // Check that we have the expected output
        $this->assertStringContainsString('Thanks for playing!', $output[0]);
        $this->assertStringContainsString("Run 'rps' to play again.", $output[1]);

        // Check that the command is no longer interactive
        $this->assertFalse($this->terminal->getCommandState()->has(CommandStates::INTERACTIVE));
    }

    public function test_command_determines_winner_correctly()
    {
        $method = new ReflectionMethod(RockPaperScissorsCommand::class, 'determineWinner');
        $method->setAccessible(true);

        // Test rock beats scissors
        $this->assertEquals('win', $method->invoke($this->command, '1', '3'));

        // Test paper beats rock
        $this->assertEquals('win', $method->invoke($this->command, '2', '1'));

        // Test scissors beats paper
        $this->assertEquals('win', $method->invoke($this->command, '3', '2'));

        // Test rock loses to paper
        $this->assertEquals('lose', $method->invoke($this->command, '1', '2'));

        // Test paper loses to scissors
        $this->assertEquals('lose', $method->invoke($this->command, '2', '3'));

        // Test scissors loses to rock
        $this->assertEquals('lose', $method->invoke($this->command, '3', '1'));

        // Test ties
        $this->assertEquals('tie', $method->invoke($this->command, '1', '1'));
        $this->assertEquals('tie', $method->invoke($this->command, '2', '2'));
        $this->assertEquals('tie', $method->invoke($this->command, '3', '3'));
    }
}
