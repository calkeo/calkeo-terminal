<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;

class ContactCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'contact';
    protected $description = 'Send a message via email';

    // Contact information
    protected $email = 'contact@example.com';
    protected $contactName = 'Your Name';

    // Session keys
    protected const SUBJECT_KEY = 'subject';
    protected const MESSAGE_KEY = 'message';

    // Step definitions
    protected const STEP_SUBJECT = 1;
    protected const STEP_SUBJECT_CONFIRM = 2;
    protected const STEP_MESSAGE = 3;
    protected const STEP_MESSAGE_CONFIRM = 4;

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        // Get current step from session
        $step = $this->getCurrentStep();

        // If we have arguments or are in the middle of a process, handle the step
        if (!empty($args) || $step > 1) {
            return $this->handleStep($args, $step);
        }

        // Start the interactive process
        return $this->startInteractiveProcess();
    }

    protected function getSessionKeys(): array
    {
        return [
            self::SUBJECT_KEY,
            self::MESSAGE_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_SUBJECT);

        return $this->interactiveOutput([
            $this->formatOutput("Contact Form", 'header'),
            $this->formatOutput("=================", 'info'),
            "",
            $this->formatOutput("You are about to send a message to {$this->contactName} ({$this->email})", 'info'),
            "",
            $this->formatOutput("Please enter the subject of your message:", 'warning'),
        ]);
    }

    protected function handleStep(array $args, int $step): array
    {
        $input = implode(' ', $args);

        switch ($step) {
            case self::STEP_SUBJECT:
                return $this->handleSubjectStep($input);
            case self::STEP_SUBJECT_CONFIRM:
                return $this->handleSubjectConfirmation($input);
            case self::STEP_MESSAGE:
                return $this->handleMessageStep($input);
            case self::STEP_MESSAGE_CONFIRM:
                return $this->handleMessageConfirmation($input);
            default:
                return [
                    $this->formatOutput("Error: Invalid step", 'error'),
                ];
        }
    }

    protected function handleSubjectStep(string $input): array
    {
        if (empty($input)) {
            return $this->interactiveOutput([
                $this->formatOutput("Subject cannot be empty. Please try again:", 'error'),
            ]);
        }

        // Store subject in session
        $this->setSessionValue(self::SUBJECT_KEY, $input);
        $this->setCurrentStep(self::STEP_SUBJECT_CONFIRM);

        return $this->interactiveOutput([
            $this->formatOutput("Subject: " . $input, 'value'),
            "",
            $this->formatOutput("Is this correct? (yes/no):", 'warning'),
        ]);
    }

    protected function handleSubjectConfirmation(string $input): array
    {
        $input = strtolower(trim($input));

        if ($input === 'yes' || $input === 'y') {
            $this->setCurrentStep(self::STEP_MESSAGE);

            return $this->interactiveOutput([
                $this->formatOutput("Great! Now please enter your message:", 'success'),
            ]);
        } else {
            // Reset to subject step
            $this->setCurrentStep(self::STEP_SUBJECT);

            return $this->interactiveOutput([
                $this->formatOutput("Let's try again. Please enter the subject:", 'warning'),
            ]);
        }
    }

    protected function handleMessageStep(string $input): array
    {
        if (empty($input)) {
            return $this->interactiveOutput([
                $this->formatOutput("Message cannot be empty. Please try again:", 'error'),
            ]);
        }

        // Store message in session
        $this->setSessionValue(self::MESSAGE_KEY, $input);
        $this->setCurrentStep(self::STEP_MESSAGE_CONFIRM);

        return $this->interactiveOutput([
            $this->formatOutput("Message:", 'value'),
            $this->formatOutput($input, 'info'),
            "",
            $this->formatOutput("Is this correct? (yes/no):", 'warning'),
        ]);
    }

    protected function handleMessageConfirmation(string $input): array
    {
        $input = strtolower(trim($input));

        if ($input === 'yes' || $input === 'y') {
            // Get subject and message from session
            $subject = $this->getSessionValue(self::SUBJECT_KEY, '');
            $message = $this->getSessionValue(self::MESSAGE_KEY, '');

            // Create mailto link
            $mailtoLink = $this->createMailtoLink($subject, $message);

            // Clear session data
            $this->clearSession();

            return [
                $this->formatOutput("Perfect! Your message is ready to be sent.", 'success'),
                "",
                $this->formatOutput("Click the link below to open your email client:", 'info'),
                $this->formatOutput("<a href=\"{$mailtoLink}\" class=\"text-blue-400 hover:underline\">Send Email</a>", 'command'),
                "",
                $this->formatOutput("Thank you for your message!", 'success'),
            ];
        } else {
            // Reset to message step
            $this->setCurrentStep(self::STEP_MESSAGE);

            return $this->interactiveOutput([
                $this->formatOutput("Let's try again. Please enter your message:", 'warning'),
            ]);
        }
    }

    protected function createMailtoLink(string $subject, string $message): string
    {
        $params = [
            'subject' => $subject,
            'body' => $message,
        ];

        return 'mailto:' . $this->email . '?' . http_build_query($params);
    }
}
