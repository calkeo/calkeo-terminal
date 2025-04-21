<?php

namespace App\Commands;

use Illuminate\Support\Carbon;

class PrivacyCommand extends AbstractCommand
{
    protected $name = 'privacy';
    protected $description = 'Show the privacy policy of the application';

    public function execute(array $args = []): array
    {
        $lastUpdated = Carbon::parse('2025-04-21')->format('F j, Y');

        $output = [];

        $output[] = $this->formatOutput('PRIVACY POLICY', 'header');
        $output[] = $this->formatOutput('Last Updated: ' . $lastUpdated, 'info');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('1. INTRODUCTION', 'header');
        $output[] = $this->formatOutput('Welcome to calkeo.dev. This Privacy Policy explains how I collect, use, disclose, and safeguard your information when you use this terminal-based web application. Please read this privacy policy carefully. If you do not agree with the terms of this privacy policy, please do not access the application.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('2. INFORMATION WE COLLECT', 'header');
        $output[] = $this->formatOutput('2.1 Personal Information', 'subheader');
        $output[] = $this->formatOutput('I do not collect any personal information from you. I do not require you to provide any personal information to use this application. In fact, there is no way for you to give me any personal information.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('2.2 Usage Data', 'subheader');
        $output[] = $this->formatOutput('I may collect information about how you use the application, including:', 'white');
        $output[] = $this->formatOutput('- Commands executed', 'white');
        $output[] = $this->formatOutput('- Command history', 'white');
        $output[] = $this->formatOutput('- Session duration', 'white');
        $output[] = $this->formatOutput('- Browser information', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('All of this data is anonymous, encrypted and is deleted after a few days.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('3. HOW I USE YOUR INFORMATION', 'header');
        $output[] = $this->formatOutput('I may use the information I collect for various purposes, including to:', 'white');
        $output[] = $this->formatOutput('- Provide, operate, and maintain this application. In other words, to fix any errors and improve any performance issues.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('4. DISCLOSURE OF YOUR INFORMATION', 'header');
        $output[] = $this->formatOutput('I do not store any information about you. This means there is nothing that can be disclosed.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('5. SECURITY OF YOUR INFORMATION', 'header');
        $output[] = $this->formatOutput('I implement appropriate technical and organisational security measures designed to protect the security of any personal information I process. However, please also remember that I cannot guarantee that the internet itself is 100% secure. Although I will do my best to protect your personal information, transmission of personal information to and from this application is at your own risk.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('6. DATA RETENTION', 'header');
        $output[] = $this->formatOutput('Any data collected during your session (like commands you run) is stored temporarily and automatically deleted after 2 hours of inactivity. This is handled by our secure session management system.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('7. CHILDREN\'S PRIVACY', 'header');
        $output[] = $this->formatOutput('This application is not intended for use by children under the age of 13. I do not knowingly collect personal information from children under 13. If you become aware that a child has provided me with personal information, please contact me.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('8. CHANGES TO THIS PRIVACY POLICY', 'header');
        $output[] = $this->formatOutput('I may update this Privacy Policy from time to time. I will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last Updated" date at the top of this Privacy Policy.', 'white');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('9. CONTACT ME', 'header');
        $output[] = $this->formatOutput('If you have any questions about this Privacy Policy, please contact me at:', 'white');
        $output[] = $this->formatOutput('Email: mail@calkeo.dev', 'info');
        $output[] = $this->lineBreak();

        $output[] = $this->formatOutput('Thank you for using calkeo.dev', 'success');

        return $output;
    }
}
