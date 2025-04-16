<?php

use App\Commands\ContactCommand;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class ContactCommandTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Set up the session facade
        $this->app->instance('session', new \Illuminate\Session\SessionManager($this->app));
    }

    public function test_contact_command_shows_initial_form()
    {
        // Clear any existing session data
        Session::forget(['contact_subject', 'contact_message', 'contact_step']);

        $command = new ContactCommand();
        $output = $command->execute();

        // Check that the output contains the initial form
        $this->assertCount(7, $output);
        $this->assertStringContainsString('Contact Form', $output[0]);
        $this->assertStringContainsString('You are about to send a message to', $output[3]);
        $this->assertStringContainsString('Please enter the subject of your message:', $output[5]);

        // Check that the session was initialized
        $this->assertEquals(1, Session::get('contact_step'));
    }

    public function test_contact_command_handles_subject_input()
    {
        // Set up the session to simulate being in the subject input step
        Session::put('contact_step', 1);

        $command = new ContactCommand();
        $output = $command->execute(['Hello', 'World']);

        // Check that the output contains the subject confirmation
        $this->assertStringContainsString('Subject: Hello World', $output[0]);
        $this->assertStringContainsString('Is this correct? (yes/no):', $output[2]);

        // Check that the session was updated
        $this->assertEquals('Hello World', Session::get('contact_subject'));
        $this->assertEquals(2, Session::get('contact_step'));
    }

    public function test_contact_command_handles_subject_confirmation_yes()
    {
        // Set up the session to simulate being in the subject confirmation step
        Session::put('contact_step', 2);
        Session::put('contact_subject', 'Test Subject');

        $command = new ContactCommand();
        $output = $command->execute(['yes']);

        // Check that the output contains the message input prompt
        $this->assertStringContainsString('Great! Now please enter your message:', $output[0]);

        // Check that the session was updated
        $this->assertEquals(3, Session::get('contact_step'));
    }

    public function test_contact_command_handles_subject_confirmation_no()
    {
        // Set up the session to simulate being in the subject confirmation step
        Session::put('contact_step', 2);
        Session::put('contact_subject', 'Test Subject');

        $command = new ContactCommand();
        $output = $command->execute(['no']);

        // Check that the output contains the subject input prompt again
        $this->assertStringContainsString("Let's try again. Please enter the subject:", $output[0]);

        // Check that the session was updated
        $this->assertEquals(1, Session::get('contact_step'));
    }

    public function test_contact_command_handles_message_input()
    {
        // Set up the session to simulate being in the message input step
        Session::put('contact_step', 3);

        $command = new ContactCommand();
        $output = $command->execute(['This', 'is', 'a', 'test', 'message']);

        // Check that the output contains the message confirmation
        $this->assertStringContainsString('Message:', $output[0]);
        $this->assertStringContainsString('This is a test message', $output[1]);
        $this->assertStringContainsString('Is this correct? (yes/no):', $output[3]);

        // Check that the session was updated
        $this->assertEquals('This is a test message', Session::get('contact_message'));
        $this->assertEquals(4, Session::get('contact_step'));
    }

    public function test_contact_command_handles_message_confirmation_yes()
    {
        // Set up the session to simulate being in the message confirmation step
        Session::put('contact_step', 4);
        Session::put('contact_subject', 'Test Subject');
        Session::put('contact_message', 'Test Message');

        $command = new ContactCommand();
        $output = $command->execute(['yes']);

        // Check that the output contains the mailto link
        $this->assertStringContainsString('Perfect! Your message is ready to be sent.', $output[0]);
        $this->assertStringContainsString('Click the link below to open your email client:', $output[2]);
        $this->assertStringContainsString('Send Email', $output[3]);
        $this->assertStringContainsString('mailto:', $output[3]);
        $this->assertStringContainsString('subject=Test+Subject', $output[3]);
        $this->assertStringContainsString('body=Test+Message', $output[3]);

        // Check that the session was cleared
        $this->assertNull(Session::get('contact_subject'));
        $this->assertNull(Session::get('contact_message'));
        $this->assertNull(Session::get('contact_step'));
    }

    public function test_contact_command_handles_message_confirmation_no()
    {
        // Set up the session to simulate being in the message confirmation step
        Session::put('contact_step', 4);
        Session::put('contact_subject', 'Test Subject');
        Session::put('contact_message', 'Test Message');

        $command = new ContactCommand();
        $output = $command->execute(['no']);

        // Check that the output contains the message input prompt again
        $this->assertStringContainsString("Let's try again. Please enter your message:", $output[0]);

        // Check that the session was updated
        $this->assertEquals(3, Session::get('contact_step'));
    }

    public function test_contact_command_handles_empty_subject()
    {
        // Set up the session to simulate being in the subject input step
        Session::put('contact_step', 1);

        $command = new ContactCommand();
        $output = $command->execute(['']);

        // Check that the output contains the error message
        $this->assertStringContainsString('Subject cannot be empty. Please try again:', $output[0]);

        // Check that the session was not updated
        $this->assertEquals(1, Session::get('contact_step'));
    }

    public function test_contact_command_handles_empty_message()
    {
        // Set up the session to simulate being in the message input step
        Session::put('contact_step', 3);

        $command = new ContactCommand();
        $output = $command->execute(['']);

        // Check that the output contains the error message
        $this->assertStringContainsString('Message cannot be empty. Please try again:', $output[0]);

        // Check that the session was not updated
        $this->assertEquals(3, Session::get('contact_step'));
    }
}
