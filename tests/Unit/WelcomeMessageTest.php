<?php

namespace Tests\Unit;

use App\Commands\WelcomeMessage;
use Tests\TestCase;

class WelcomeMessageTest extends TestCase
{
    /**
     * Test that the WelcomeMessage command displays the correct app name and version.
     *
     * @return void
     */
    public function test_welcome_message_displays_version_and_name()
    {
        $welcomeMessage = new WelcomeMessage();
        $formattedMessage = $welcomeMessage->format();

        $this->assertStringContainsString(config('app.name'), $formattedMessage);
        $this->assertStringContainsString(config('app.version'), $formattedMessage);
    }
}
