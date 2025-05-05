<?php

namespace Tests\Unit\Commands;

use App\Commands\ForbiddenCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Facades\Session;
use ReflectionClass;
use Tests\TestCase;
use Tests\Traits\TerminalTestTrait;

class ForbiddenCommandTest extends TestCase
{
    use TerminalTestTrait;

    protected $command;
    protected $terminal;
    protected $reflection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new ForbiddenCommand();
        $this->terminal = $this->initializeTerminal();
        $this->reflection = new ReflectionClass(ForbiddenCommand::class);
        $this->clearSession();
    }

    protected function tearDown(): void
    {
        $this->clearSession();
        parent::tearDown();
    }

    protected function clearSession(): void
    {
        Session::forget([
            $this->getSessionKey($this->getConstant('COMMAND_KEY')),
        ]);
        Session::forget($this->getStepKey());
    }

    protected function getConstant(string $name)
    {
        return $this->reflection->getConstant($name);
    }

    protected function getSessionKey(string $key): string
    {
        return sprintf('%s_%s', 'forbidden', $key);
    }

    protected function getStepKey(): string
    {
        return sprintf('%s_step', 'forbidden');
    }

    public function test_command_has_correct_name_and_description()
    {
        $this->assertEquals('forbidden', $this->command->getName());
        $this->assertEquals('Detect and block potentially dangerous commands', $this->command->getDescription());
    }

    public function test_command_is_hidden()
    {
        $this->assertTrue($this->command->isHidden());
    }

    public function test_command_starts_with_warning_message()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('⚠️', $output[0]);
        $this->assertStringContainsString('Type \'yes\' to confirm or \'no\' to cancel:', $output[1]);
    }

    public function test_command_handles_yes_response()
    {
        // First execute to get to confirmation step
        $this->command->execute($this->terminal);

        // Respond with yes
        $output = $this->command->execute($this->terminal, ['yes']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('...', $output[0]); // Proceed message ends with ...
    }

    public function test_command_handles_no_response()
    {
        // First execute to get to confirmation step
        $this->command->execute($this->terminal);

        // Respond with no
        $output = $this->command->execute($this->terminal, ['no']);

        $this->assertNotEmpty($output);
        // Check for any of the success messages
        $this->assertTrue(
            str_contains($output[0], 'Wise decision') ||
            str_contains($output[0], 'Good call') ||
            str_contains($output[0], 'Smart thinking') ||
            str_contains($output[0], 'Prudent choice') ||
            str_contains($output[0], 'Good judgment') ||
            str_contains($output[0], 'Responsible choice') ||
            str_contains($output[0], 'Smart move') ||
            str_contains($output[0], 'Good thinking') ||
            str_contains($output[0], 'Wise choice') ||
            str_contains($output[0], 'Good decision')
        );
    }

    public function test_command_handles_invalid_response()
    {
        // First execute to get to confirmation step
        $this->command->execute($this->terminal);

        // Try invalid input
        $output = $this->command->execute($this->terminal, ['maybe']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString("Please type 'yes' or 'no':", $output[0]);
    }

    public function test_command_handles_yes_shortcut()
    {
        // First execute to get to confirmation step
        $this->command->execute($this->terminal);

        // Respond with y
        $output = $this->command->execute($this->terminal, ['y']);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('...', $output[0]); // Proceed message ends with ...
    }

    public function test_command_handles_no_shortcut()
    {
        // First execute to get to confirmation step
        $this->command->execute($this->terminal);

        // Respond with n
        $output = $this->command->execute($this->terminal, ['n']);

        $this->assertNotEmpty($output);
        // Check for any of the success messages
        $this->assertTrue(
            str_contains($output[0], 'Wise decision') ||
            str_contains($output[0], 'Good call') ||
            str_contains($output[0], 'Smart thinking') ||
            str_contains($output[0], 'Prudent choice') ||
            str_contains($output[0], 'Good judgment') ||
            str_contains($output[0], 'Responsible choice') ||
            str_contains($output[0], 'Smart move') ||
            str_contains($output[0], 'Good thinking') ||
            str_contains($output[0], 'Wise choice') ||
            str_contains($output[0], 'Good decision')
        );
    }
}
