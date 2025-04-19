<?php

namespace Tests\Unit;

use Tests\TestCase;

class ConfigTest extends TestCase
{
    /**
     * Test that the app name and version are correctly set in the config.
     *
     * @return void
     */
    public function test_app_config_values()
    {
        $this->assertEquals('calkeOS', config('app.name'));
        $this->assertEquals('v0.1.0', config('app.version'));
    }
}
