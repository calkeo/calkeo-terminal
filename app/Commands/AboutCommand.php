<?php

namespace App\Commands;

use Illuminate\Support\Carbon;

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

        $yearsFloat = Carbon::parse('2017-05-01')->diffInYears();
        $yearsInt = floor($yearsFloat);
        $decimalPart = $yearsFloat - $yearsInt;

        if ($decimalPart > 0.8) {
            $yearsText = "almost " . ($yearsInt + 1);
        } else {
            $yearsText = "over {$yearsInt}";
        }

        $output[] = $this->formatOutput('About Me', 'info');
        $output[] = '==========';
        $output[] = $this->formatOutput('', 'white');
        $output[] = $this->formatOutput('I\'m Callum Keogan — Senior Developer at Shopify. I keep the wheels of global commerce turning by day and wonder why my personal projects have so many TODOs by night.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput("With {$yearsText} years in the industry, I'm currently working on the Checkout team at Shopify, where my work impacts millions of businesses across 175+ countries.", 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('My developer journey has led me through various teams at Shopify, including leading development on the Shopify Forms app. What started with just two engineers grew to a team of 14 across eight countries. We built something that simultaneously improves merchant-customer connections while maintaining robust security within the Shopify ecosystem.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('These days, I\'m tackling architectural challenges on the Checkout team, replatforming legacy systems to enhance extensibility and integration capabilities. I\'ve also done significant work integrating Draft Orders with other Shopify systems — building connections across our platform that make merchants\' lives easier.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('What drives me? I thrive on working across large, complex systems, crafting code that helps real people run their businesses more effectively, which is reflected in this terminal-inspired site where form follows function.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('I\'m passionate about creating applications that are both secure-by-design and respectful of user privacy. When I\'m not coding, you\'ll likely find me exploring trails with my dog.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Skills', 'success');
        $output[] = '------';
        $output[] = $this->formatOutput('- Languages: PHP, Ruby', 'white');
        $output[] = $this->formatOutput('- Frameworks: Laravel, Ruby on Rails', 'white');
        $output[] = $this->formatOutput('- Databases: MySQL', 'white');
        $output[] = $this->formatOutput('- APIs: GraphQL, REST', 'white');
        $output[] = $this->formatOutput('- Tools: Git, GitHub Actions', 'white');
        $output[] = $this->formatOutput('- Testing: PHPUnit, Minitest', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Professional Values', 'info');
        $output[] = '-------------------';
        $output[] = $this->formatOutput('Code Craftsmanship', 'white');
        $output[] = $this->formatOutput('- Building systems that stand the test of time, not just the next sprint', 'white');
        $output[] = $this->formatOutput('- Finding elegance in complexity – creating solutions that are both powerful and maintainable', 'white');
        $output[] = $this->formatOutput('- Embracing constraints as creative challenges rather than limitations', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Impact & Integration', 'white');
        $output[] = $this->formatOutput('- Creating code that connects seamlessly across systems and teams', 'white');
        $output[] = $this->formatOutput('- Measuring success by merchant outcomes, not just technical metrics', 'white');
        $output[] = $this->formatOutput('- Building features that solve real problems, not just check requirement boxes', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Security & Trust', 'white');
        $output[] = $this->formatOutput('- Designing with security as a foundational element, not an afterthought', 'white');
        $output[] = $this->formatOutput('- Respecting user data as if it were my own', 'white');
        $output[] = $this->formatOutput('- Creating systems that are resilient against both current and emerging threats', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Growth Mindset', 'white');
        $output[] = $this->formatOutput('- Approaching code reviews as opportunities for collective improvement', 'white');
        $output[] = $this->formatOutput('- Sharing knowledge freely without gatekeeping or jargon', 'white');
        $output[] = $this->formatOutput('- Balancing pragmatism with innovation – knowing when to perfect and when to ship', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Engineering Excellence', 'white');
        $output[] = $this->formatOutput('- Writing tests that validate behavior, not just implementation', 'white');
        $output[] = $this->formatOutput('- Documenting with empathy for future developers (including future me)', 'white');
        $output[] = $this->formatOutput('- Optimizing for maintainability alongside performance', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Technical Leadership & Team Contributions', 'warning');
        $output[] = '--------------------------------';
        $output[] = $this->formatOutput('Previous Leadership Role', 'white');
        $output[] = $this->formatOutput('At NE6, I served as Head of Development, managing project timelines and coordinating with clients to translate their requirements into technical solutions.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Project Contributions', 'white');
        $output[] = $this->formatOutput('I\'ve contributed to long-term projects where technical decisions had lasting impact, helping guide implementation while collaborating with team members across different levels of experience.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Shopify Forms Evolution', 'white');
        $output[] = $this->formatOutput('As one of the original developers on Shopify Forms, I witnessed and contributed to its growth from 2 to 14 engineers across 8 countries, participating in the team\'s expansion while maintaining code quality and architectural integrity.', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Senior Developer Approach', 'white');
        $output[] = $this->formatOutput('- Sharing technical knowledge with peers when it adds value', 'white');
        $output[] = $this->formatOutput('- Providing thoughtful code reviews that help teammates grow', 'white');
        $output[] = $this->formatOutput('- Contributing to architectural discussions with practical, implementation-focused insights', 'white');
        $output[] = $this->formatOutput('- Balancing immediate needs against long-term maintainability', 'white');
        $output[] = $this->lineBreak();
        $output[] = $this->formatOutput('Collaborative Mindset', 'white');
        $output[] = $this->formatOutput('I believe effective technical contributions come from understanding both the code and its context—working alongside teammates to build solutions that address real business challenges while maintaining engineering excellence.', 'white');

        return $output;
    }
}