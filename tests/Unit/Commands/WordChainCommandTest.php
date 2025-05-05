<?php

namespace Tests\Unit\Commands;

use App\Commands\WordChainCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\TerminalTestTrait;

class WordChainCommandTest extends TestCase
{
    use TerminalTestTrait;

    protected $command;
    protected $terminal;
    protected $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new WordChainCommand();
        $this->terminal = $this->initializeTerminal();
        $this->reflection = new ReflectionClass(WordChainCommand::class);
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
            $this->getSessionKey($this->getConstant('CHAIN_KEY')),
            $this->getSessionKey($this->getConstant('CURRENT_PLAYER_KEY')),
            $this->getSessionKey($this->getConstant('GAME_OVER_KEY')),
            $this->getSessionKey($this->getConstant('DIFFICULTY_KEY')),
            $this->getSessionKey($this->getConstant('USED_WORDS_KEY')),
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
        return sprintf('%s_%s', 'wordchain', $key);
    }

    protected function getStepKey(): string
    {
        return sprintf('%s_step', 'wordchain');
    }

    protected function setupGameState(array $state = []): void
    {
        $defaults = [
            'step' => $this->getConstant('STEP_PLAY'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'current_player' => 'human',
            'chain' => [],
            'used_words' => [],
            'game_over' => false,
        ];

        $state = array_merge($defaults, $state);

        Session::put($this->getStepKey(), $state['step']);
        Session::put($this->getSessionKey($this->getConstant('DICTIONARY_KEY')), $state['dictionary']);
        Session::put($this->getSessionKey($this->getConstant('DIFFICULTY_KEY')), $state['difficulty']);
        Session::put($this->getSessionKey($this->getConstant('CURRENT_PLAYER_KEY')), $state['current_player']);
        Session::put($this->getSessionKey($this->getConstant('CHAIN_KEY')), $state['chain']);
        Session::put($this->getSessionKey($this->getConstant('USED_WORDS_KEY')), $state['used_words']);
        Session::put($this->getSessionKey($this->getConstant('GAME_OVER_KEY')), $state['game_over']);
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('wordchain', $this->command->getName());
        $this->assertEquals('Play Word Chain Game', $this->command->getDescription());
    }

    public function test_command_has_correct_aliases()
    {
        $aliases = $this->command->getAliases();
        $this->assertContains('chain', $aliases);
    }

    public function test_command_is_hidden()
    {
        $this->assertTrue($this->command->isHidden());
    }

    public function test_command_starts_with_dictionary_selection()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Word Chain Game', $output[0]);
        $this->assertStringContainsString('Choose your dictionary:', $output[13]);
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
        $this->assertStringContainsString("Let's start! Enter your first word:", $output[2]);
    }

    public function test_command_validates_word_length()
    {
        // Set up play step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'current_player' => 'human',
            'chain' => [],
            'used_words' => [],
        ]);

        // Try word that's too short
        $output = $this->command->execute($this->terminal, ['hi']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Word must be at least 3 letters long!', $output[0]);
    }

    public function test_command_validates_word_against_dictionary()
    {
        // Set up play step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'current_player' => 'human',
            'chain' => [],
            'used_words' => [],
        ]);

        // Try invalid word
        $output = $this->command->execute($this->terminal, ['xyzzy']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString("That's not a valid word! Try another word:", $output[0]);
    }

    public function test_command_validates_word_chain_rule()
    {
        // Set up play step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'current_player' => 'human',
            'chain' => ['cat'],
            'used_words' => ['cat'],
        ]);

        // Try word that doesn't follow chain rule
        $output = $this->command->execute($this->terminal, ['dog']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString("Word must start with 'T'!", $output[0]);
    }

    public function test_command_handles_game_over()
    {
        // Set up play again step
        $this->setupGameState([
            'step' => $this->getConstant('STEP_PLAY_AGAIN'),
            'dictionary' => 'GB',
            'difficulty' => 'easy',
            'current_player' => 'human',
            'game_over' => true,
        ]);

        $output = $this->command->execute($this->terminal, ['no']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Thanks for playing!', $output[0]);
    }
}
