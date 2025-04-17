<?php

namespace Tests\Unit\Livewire;

use App\Livewire\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up default test taglines
        $this->app['config']->set('terminal.taglines', [
            'Tagline 1',
            'Tagline 2',
            'Tagline 3',
        ]);
    }

    /**
     * Test that the component loads with a random tagline from config
     */
    public function test_component_loads_with_random_tagline()
    {
        $component = Livewire::test(Login::class);

        $this->assertNotNull($component->get('tagline'));
        $this->assertContains($component->get('tagline'), [
            'Tagline 1',
            'Tagline 2',
            'Tagline 3',
        ]);
    }

    /**
     * Test that the component handles empty taglines gracefully
     */
    public function test_component_handles_empty_taglines()
    {
        $this->app['config']->set('terminal.taglines', []);

        $component = Livewire::test(Login::class);

        $this->assertNotNull($component->get('tagline'));
        $this->assertEquals('', $component->get('tagline'));
    }

    /**
     * Test that the component renders with the tagline
     */
    public function test_component_renders_with_tagline()
    {
        $this->app['config']->set('terminal.taglines', ['Test Tagline']);

        $component = Livewire::test(Login::class);
        $component->assertSee('Test Tagline');
    }
}
