<?php

namespace Tests\Unit\Commands;

use App\Commands\HangmanCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\TerminalTestTrait;

class HangmanCommandTest extends TestCase
{
    use TerminalTestTrait;

    protected $command;
    protected $terminal;
    protected $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new HangmanCommand();
        $this->terminal = $this->initializeTerminal();
        $this->reflection = new ReflectionClass(HangmanCommand::class);
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
            $this->getSessionKey($this->getConstant('WORD_KEY')),
            $this->getSessionKey($this->getConstant('GUESSED_LETTERS_KEY')),
            $this->getSessionKey($this->getConstant('REMAINING_GUESSES_KEY')),
            $this->getSessionKey($this->getConstant('GAME_OVER_KEY')),
            $this->getSessionKey($this->getConstant('WINNER_KEY')),
            $this->getSessionKey($this->getConstant('DIFFICULTY_KEY')),
            $this->getSessionKey($this->getConstant('DICTIONARY_KEY')),
        ]);
        Session::forget($this->getStepKey());
        Cache::flush();
    }

    protected function getConstant(string $name)
    {
        return $this->reflection->getConstant($name);
    }

    protected function getSessionKey(string $key): string
    {
        return sprintf('%s_%s', 'hangman', $key);
    }

    protected function getStepKey(): string
    {
        return sprintf('%s_step', 'hangman');
    }

    protected function setupGameState(array $state = []): void
    {
        $defaults = [
            'step' => $this->getConstant('STEP_DICTIONARY'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'word' => 'test',
            'guessed_letters' => [],
            'remaining_guesses' => 6,
            'game_over' => false,
            'winner' => null,
        ];

        $state = array_merge($defaults, $state);

        Session::put($this->getStepKey(), $state['step']);
        Session::put($this->getSessionKey($this->getConstant('DICTIONARY_KEY')), $state['dictionary']);
        Session::put($this->getSessionKey($this->getConstant('DIFFICULTY_KEY')), $state['difficulty']);
        Session::put($this->getSessionKey($this->getConstant('WORD_KEY')), $state['word']);
        Session::put($this->getSessionKey($this->getConstant('GUESSED_LETTERS_KEY')), $state['guessed_letters']);
        Session::put($this->getSessionKey($this->getConstant('REMAINING_GUESSES_KEY')), $state['remaining_guesses']);
        Session::put($this->getSessionKey($this->getConstant('GAME_OVER_KEY')), $state['game_over']);
        Session::put($this->getSessionKey($this->getConstant('WINNER_KEY')), $state['winner']);
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('hangman', $this->command->getName());
        $this->assertEquals('Play Hangman Game', $this->command->getDescription());
    }

    public function test_command_has_correct_aliases()
    {
        $aliases = $this->command->getAliases();
        $this->assertContains('hangman', $aliases);
    }

    public function test_command_is_hidden()
    {
        $this->assertTrue($this->command->isHidden());
    }

    public function test_command_starts_with_dictionary_selection()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Hangman Game', $output[0]);
        $this->assertStringContainsString('Choose your dictionary:', $output[3]);
        $this->assertStringContainsString('Enter your choice (1-4):', $output[count($output) - 1]);
    }

    public function test_command_handles_invalid_dictionary_selection()
    {
        // First execute to get to dictionary selection
        $this->command->execute($this->terminal);

        // Try invalid selection
        $output = $this->command->execute($this->terminal, ['5']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Invalid choice! Please enter 1, 2, 3, or 4:', $output[0]);
    }

    public function test_command_proceeds_to_difficulty_selection_after_valid_dictionary()
    {
        // Set up dictionary selection step
        $this->setupGameState(['step' => $this->getConstant('STEP_DICTIONARY')]);

        // Select dictionary
        $output = $this->command->execute($this->terminal, ['1']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Dictionary: GB', $output[0]);
        $this->assertStringContainsString('Choose difficulty level:', $output[2]);
        $this->assertStringContainsString('Enter your choice (1-3):', $output[count($output) - 1]);
    }

    public function test_command_handles_invalid_difficulty_selection()
    {
        // Set up difficulty selection step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_DIFFICULTY'),
            'dictionary' => 'GB',
        ]);

        // Try invalid selection
        $output = $this->command->execute($this->terminal, ['4']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Invalid choice! Please enter 1, 2, or 3:', $output[0]);
    }

    public function test_command_starts_game_after_valid_difficulty_selection()
    {
        // Set up difficulty selection step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_DIFFICULTY'),
            'dictionary' => 'GB',
        ]);

        // Select difficulty
        $output = $this->command->execute($this->terminal, ['1']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Difficulty: Easy', $output[0]);
        $this->assertStringContainsString('Word to guess:', $output[2]);
        $this->assertStringContainsString('Enter a letter:', $output[count($output) - 1]);
    }

    public function test_command_validates_letter_input()
    {
        // Set up guess step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_GUESS'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'word' => 'test',
        ]);

        // Try invalid input (number)
        $output = $this->command->execute($this->terminal, ['1']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Invalid input! Please enter a single letter:', $output[2]);
    }

    public function test_command_handles_correct_letter_guess()
    {
        // Set up guess step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_GUESS'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'word' => 'test',
            'guessed_letters' => [],
        ]);

        // Guess correct letter
        $output = $this->command->execute($this->terminal, ['t']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Word: <span class="text-emerald-400">T</span> _ _ <span class="text-emerald-400">T</span>', $output[2]);
        $this->assertStringContainsString('Guessed letters: t', $output[3]);
    }

    public function test_command_handles_incorrect_letter_guess()
    {
        // Set up guess step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_GUESS'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'word' => 'test',
            'guessed_letters' => [],
        ]);

        // Guess incorrect letter
        $output = $this->command->execute($this->terminal, ['x']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Word: _ _ _ _', $output[2]);
        $this->assertStringContainsString('Guessed letters: x', $output[3]);
        $this->assertStringContainsString('Remaining guesses: 5', $output[4]);
    }

    public function test_command_handles_duplicate_letter_guess()
    {
        // Set up guess step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_GUESS'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'word' => 'test',
            'guessed_letters' => ['t'],
        ]);

        // Guess duplicate letter
        $output = $this->command->execute($this->terminal, ['t']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('You already guessed that letter!', $output[2]);
    }

    public function test_command_handles_win_condition()
    {
        // Set up guess step with almost complete word
        $this->setupGameState([
            'step' => $this->getConstant('STEP_GUESS'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'word' => 'test',
            'guessed_letters' => ['e', 's'],
        ]);

        // Guess last letter
        $output = $this->command->execute($this->terminal, ['t']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Congratulations! You win!', $output[2]);
        $this->assertStringContainsString('The word was: TEST', $output[3]);
        $this->assertStringContainsString('Want to play again? (yes/no):', $output[5]);
    }

    public function test_command_handles_lose_condition()
    {
        // Set up guess step with one remaining guess
        $this->setupGameState([
            'step' => $this->getConstant('STEP_GUESS'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'word' => 'test',
            'guessed_letters' => ['x', 'y', 'z', 'w', 'q'],
            'remaining_guesses' => 1,
        ]);

        // Make final incorrect guess
        $output = $this->command->execute($this->terminal, ['r']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Game Over! You lose!', $output[2]);
        $this->assertStringContainsString('The word was: TEST', $output[3]);
        $this->assertStringContainsString('Want to play again? (yes/no):', $output[5]);
    }

    public function test_command_handles_play_again_yes()
    {
        // Set up play again step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY_AGAIN'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'game_over' => true,
        ]);

        $output = $this->command->execute($this->terminal, ['yes']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString("Let's play again!", $output[0]);
        $this->assertStringContainsString('Choose your dictionary:', $output[2]);
    }

    public function test_command_handles_play_again_no()
    {
        // Set up play again step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY_AGAIN'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'game_over' => true,
        ]);

        $output = $this->command->execute($this->terminal, ['no']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Thanks for playing!', $output[0]);
        $this->assertStringContainsString("Run 'hangman' to play again.", $output[1]);
    }
}
