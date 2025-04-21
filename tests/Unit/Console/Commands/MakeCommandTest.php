<?php

namespace Tests\Unit\Console\Commands;

use App\Console\Commands\MakeCommand;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class MakeCommandTest extends TestCase
{
    protected $command;
    protected $commandsPath;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = $this->app->make(MakeCommand::class);
        $this->commandsPath = app_path('Commands');
    }

    protected function tearDown(): void
    {
        // Clean up any test files
        $testFiles = [
            $this->commandsPath . '/TestCommand.php',
            $this->commandsPath . '/InteractiveTestCommand.php',
            $this->commandsPath . '/MySpecialCommandCommand.php',
        ];

        foreach ($testFiles as $file) {
            if (File::exists($file)) {
                File::delete($file);
            }
        }

        parent::tearDown();
    }

    public function test_make_command_creates_regular_command()
    {
        // Run the command
        $this->artisan('make:command', ['name' => 'test'])
             ->expectsOutput('Command TestCommand created successfully!')
             ->assertSuccessful();

        // Check if the file was created
        $this->assertTrue(File::exists($this->commandsPath . '/TestCommand.php'));

        // Check the file contents
        $contents = File::get($this->commandsPath . '/TestCommand.php');
        $this->assertStringContainsString('namespace App\Commands;', $contents);
        $this->assertStringContainsString('class TestCommand extends AbstractCommand', $contents);
        $this->assertStringContainsString('protected $name = \'test\';', $contents);
        $this->assertStringContainsString('protected $description = \'Description of the command\';', $contents);
        $this->assertStringContainsString('public function execute(array $args = []): array', $contents);
    }

    public function test_make_command_creates_interactive_command()
    {
        // Run the command
        $this->artisan('make:command', [
                 'name' => 'interactive-test',
                 '--interactive' => true,
             ])
             ->expectsOutput('Command InteractiveTestCommand created successfully!')
             ->assertSuccessful();

        // Check if the file was created
        $this->assertTrue(File::exists($this->commandsPath . '/InteractiveTestCommand.php'));

        // Check the file contents
        $contents = File::get($this->commandsPath . '/InteractiveTestCommand.php');
        $this->assertStringContainsString('namespace App\Commands;', $contents);
        $this->assertStringContainsString('use App\Commands\Traits\InteractiveCommandTrait;', $contents);
        $this->assertStringContainsString('class InteractiveTestCommand extends AbstractCommand', $contents);
        $this->assertStringContainsString('use InteractiveCommandTrait;', $contents);
        $this->assertStringContainsString('protected $name = \'interactive-test\';', $contents);
        $this->assertStringContainsString('protected $description = \'Description of the interactive command\';', $contents);
        $this->assertStringContainsString('protected const STEP_KEY = \'step\';', $contents);
        $this->assertStringContainsString('protected const STEP_INITIAL = 1;', $contents);
        $this->assertStringContainsString('protected const STEP_PROCESS = 2;', $contents);
        $this->assertStringContainsString('public function execute(array $args = []): array', $contents);
        $this->assertStringContainsString('protected function getSessionKeys(): array', $contents);
        $this->assertStringContainsString('protected function startInteractiveProcess(): array', $contents);
        $this->assertStringContainsString('protected function handleStep(array $args, int $step): array', $contents);
    }

    public function test_make_command_fails_if_command_already_exists()
    {
        // Create a test command file
        File::put($this->commandsPath . '/TestCommand.php', '<?php namespace App\Commands; class TestCommand {}');

        // Run the command
        $this->artisan('make:command', ['name' => 'test'])
             ->expectsOutput('Command TestCommand already exists!')
             ->assertFailed();
    }

    public function test_make_command_handles_special_characters_in_name()
    {
        $commandName = 'my-special-command';

        // Run the command with special characters
        $this->artisan('make:command', ['name' => $commandName])
             ->expectsOutput('Command MySpecialCommandCommand created successfully!')
             ->assertSuccessful();

        // Check if the file was created
        $this->assertTrue(File::exists($this->commandsPath . '/MySpecialCommandCommand.php'));

        // Check the file contents
        $contents = File::get($this->commandsPath . '/MySpecialCommandCommand.php');
        $this->assertStringContainsString("protected \$name = '$commandName';", $contents);
    }
}
