<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_view_login_page()
    {
        Livewire::test(Login::class)
            ->assertSee('Welcome to')
            ->assertSee('Enter any credentials to continue');
    }

    public function test_username_is_required()
    {
        Livewire::test(Login::class)
            ->set('username', '')
            ->call('login')
            ->assertSet('error', 'Username is required');
    }

    public function test_typing_state_is_updated()
    {
        Livewire::test(Login::class)
            ->set('username', 'test')
            ->assertSet('isTyping', true)
            ->assertSet('error', '');
    }

    public function test_login_animation_starts()
    {
        Livewire::test(Login::class)
            ->set('username', 'test')
            ->call('login')
            ->assertSet('isAnimating', true)
            ->assertSet('animationStep', 0)
            ->assertSet('animationText', '');
    }

    public function test_login_animation_progresses()
    {
        Livewire::test(Login::class)
            ->set('username', 'test')
            ->call('login')
            ->call('nextAnimationStep')
            ->assertSet('animationStep', 1)
            ->assertSet('animationText', function ($text) {
                return $text !== '';
            });
    }

    public function test_login_animation_completes_and_redirects()
    {
        $component = Livewire::test(Login::class)
            ->set('username', 'test')
            ->call('login');

        // Simulate all animation steps
        $loginSets = config('terminal.login_animations');
        $steps = reset($loginSets); // Get first theme's steps
        $stepCount = count($steps) - 1; // Subtract 1 because we start at 0

        for ($i = 0; $i < $stepCount; $i++) {
            $component->call('nextAnimationStep');
        }

        // Verify session state
        $this->assertTrue(session()->has('terminal_logged_in'));
        $this->assertEquals('test', session('terminal_username'));

        // Test the final redirect
        $component->call('redirectToTerminal')
                  ->assertRedirect('/');
    }

    public function test_plain_text_view_redirects()
    {
        Livewire::test(Login::class)
            ->call('viewPlainText')
            ->assertRedirect('/');
    }
}
