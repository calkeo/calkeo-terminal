<?php

namespace App\Commands;

class AboutCommand extends AbstractCommand
{
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'about';
        $this->description = 'Learn about me';
    }

    /**
     * Execute the command
     *
     * @param  array   $args
     * @return array
     */
    public function execute(array $args = []): array
    {
        $output = [];

        $output[] = $this->formatOutput('About Me', 'info');
        $output[] = '==========';
        $output[] = '';
        $output[] = 'I am a passionate developer who loves creating elegant solutions to complex problems.';
        $output[] = '';
        $output[] = 'My journey in software development began with a curiosity about how things work.';
        $output[] = 'Over the years, I have developed expertise in various technologies and frameworks.';
        $output[] = '';
        $output[] = $this->formatOutput('Skills', 'success');
        $output[] = '------';
        $output[] = '- Languages: PHP, JavaScript, Python, Java';
        $output[] = '- Frameworks: Laravel, Vue.js, React, Spring Boot';
        $output[] = '- Databases: MySQL, PostgreSQL, MongoDB';
        $output[] = '- Tools: Git, Docker, AWS, Kubernetes';
        $output[] = '';
        $output[] = $this->formatOutput('Education', 'info');
        $output[] = '---------';
        $output[] = '- Bachelor of Science in Computer Science';
        $output[] = '- Various certifications in web development and cloud technologies';
        $output[] = '';
        $output[] = $this->formatOutput('Interests', 'warning');
        $output[] = '---------';
        $output[] = '- Open source contribution';
        $output[] = '- Technical writing and blogging';
        $output[] = '- Mentoring junior developers';
        $output[] = '- Exploring new technologies';

        return $output;
    }
}
