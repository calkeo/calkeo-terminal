<?php

namespace Tests\Feature;

use App\Livewire\Terminal;
use Livewire\Livewire;
use Tests\TestCase;

class TerminalComponentTest extends TestCase
{
    /**
     * Test that the Terminal component displays the correct app name and version.
     *
     * @return void
     */
    public function test_terminal_component_displays_version_and_name()
    {
        // Mock the session to simulate a logged-in user
        $this->withSession(['terminal_logged_in' => true]);

        Livewire::test(Terminal::class)
            ->assertSee(config('app.name'))
            ->assertSee(config('app.version'));
    }
}
