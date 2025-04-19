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

        // Calculate years in industry
        $yearsFloat = Carbon::parse('2017-05-01')->diffInYears();
        $yearsInt = floor($yearsFloat);
        $decimalPart = $yearsFloat - $yearsInt;

        if ($decimalPart > 0.8) {
            $yearsText = "almost " . ($yearsInt + 1);
        } else {
            $yearsText = "over {$yearsInt}";
        }

        // Define content structure
        $content = [
            'header' => [
                'title' => 'About Me',
                'style' => 'info',
                'separator' => '==========',
            ],
            'intro' => [
                'I\'m Callum Keogan — Senior Developer at Shopify. I keep the wheels of global commerce turning by day and wonder why my personal projects have so many TODOs by night.',
                "With {$yearsText} years in the industry, I'm currently working on the Checkout team at Shopify, where my work impacts millions of businesses across 175+ countries.",
                'My developer journey has led me through various teams at Shopify, including leading development on the Shopify Forms app. What started with just two engineers grew to a team of 14 across eight countries. We built something that simultaneously improves merchant-customer connections while maintaining robust security within the Shopify ecosystem.',
                'These days, I\'m tackling architectural challenges on the Checkout team, re-platforming legacy systems to enhance extensibility and integration capabilities. I\'ve also done significant work integrating Draft Orders with other Shopify systems — building connections across our platform that make merchants\' lives easier.',
                'What drives me? I thrive on working across large, complex systems, crafting code that helps real people run their businesses more effectively, which is reflected in this terminal-inspired site where form follows function.',
                'I\'m passionate about creating applications that are both secure-by-design and respectful of user privacy. When I\'m not coding, you\'ll likely find me exploring trails with my dog.',
            ],
            'skills' => [
                'title' => 'Skills',
                'style' => 'success',
                'separator' => '------',
                'items' => [
                    '- Languages: PHP, Ruby',
                    '- Frameworks: Laravel, Ruby on Rails',
                    '- Databases: MySQL',
                    '- APIs: GraphQL, REST',
                    '- Tools: Git, GitHub Actions',
                    '- Testing: PHPUnit, Minitest',
                ],
            ],
            'values' => [
                'title' => 'Professional Values',
                'style' => 'info',
                'separator' => '-------------------',
                'sections' => [
                    [
                        'title' => 'Code Craftsmanship',
                        'items' => [
                            '- Building systems that stand the test of time, not just the next sprint',
                            '- Finding elegance in complexity – creating solutions that are both powerful and maintainable',
                            '- Embracing constraints as creative challenges rather than limitations',
                        ],
                    ],
                    [
                        'title' => 'Impact & Integration',
                        'items' => [
                            '- Creating code that connects seamlessly across systems and teams',
                            '- Measuring success by merchant outcomes, not just technical metrics',
                            '- Building features that solve real problems, not just check requirement boxes',
                        ],
                    ],
                    [
                        'title' => 'Security & Trust',
                        'items' => [
                            '- Designing with security as a foundational element, not an afterthought',
                            '- Respecting user data as if it were my own',
                            '- Creating systems that are resilient against both current and emerging threats',
                        ],
                    ],
                    [
                        'title' => 'Growth Mindset',
                        'items' => [
                            '- Approaching code reviews as opportunities for collective improvement',
                            '- Sharing knowledge freely without gatekeeping or jargon',
                            '- Balancing pragmatism with innovation – knowing when to perfect and when to ship',
                        ],
                    ],
                    [
                        'title' => 'Engineering Excellence',
                        'items' => [
                            '- Writing tests that validate behavior, not just implementation',
                            '- Documenting with empathy for future developers (including future me)',
                            '- Optimizing for maintainability alongside performance',
                        ],
                    ],
                ],
            ],
            'leadership' => [
                'title' => 'Technical Leadership & Team Contributions',
                'style' => 'warning',
                'separator' => '--------------------------------',
                'sections' => [
                    [
                        'title' => 'Previous Leadership Role',
                        'content' => 'At NE6, I served as Head of Development, managing project timelines and coordinating with clients to translate their requirements into technical solutions.',
                    ],
                    [
                        'title' => 'Project Contributions',
                        'content' => 'I\'ve contributed to long-term projects where technical decisions had lasting impact, helping guide implementation while collaborating with team members across different levels of experience.',
                    ],
                    [
                        'title' => 'Shopify Forms Evolution',
                        'content' => 'As one of the original developers on Shopify Forms, I witnessed and contributed to its growth from 2 to 14 engineers across 8 countries, participating in the team\'s expansion while maintaining code quality and architectural integrity.',
                    ],
                    [
                        'title' => 'Senior Developer Approach',
                        'items' => [
                            '- Sharing technical knowledge with peers when it adds value',
                            '- Providing thoughtful code reviews that help teammates grow',
                            '- Contributing to architectural discussions with practical, implementation-focused insights',
                            '- Balancing immediate needs against long-term maintainability',
                        ],
                    ],
                    [
                        'title' => 'Collaborative Mindset',
                        'content' => 'I believe effective technical contributions come from understanding both the code and its context—working alongside teammates to build solutions that address real business challenges while maintaining engineering excellence.',
                    ],
                ],
            ],
        ];

        // Process content and generate output
        $output[] = $this->formatOutput($content['header']['title'], $content['header']['style']);
        $output[] = $content['header']['separator'];
        $output[] = $this->formatOutput('', 'white');

        // Add intro paragraphs
        foreach ($content['intro'] as $paragraph) {
            $output[] = $this->formatOutput($paragraph, 'white');
            $output[] = $this->lineBreak();
        }

        // Add skills section
        $output[] = $this->formatOutput($content['skills']['title'], $content['skills']['style']);
        $output[] = $content['skills']['separator'];
        foreach ($content['skills']['items'] as $item) {
            $output[] = $this->formatOutput($item, 'white');
        }
        $output[] = $this->lineBreak();

        // Add values section
        $output[] = $this->formatOutput($content['values']['title'], $content['values']['style']);
        $output[] = $content['values']['separator'];
        foreach ($content['values']['sections'] as $section) {
            $output[] = $this->formatOutput($section['title'], 'white');
            if (isset($section['items'])) {
                foreach ($section['items'] as $item) {
                    $output[] = $this->formatOutput($item, 'white');
                }
            }
            $output[] = $this->lineBreak();
        }

        // Add leadership section
        $output[] = $this->formatOutput($content['leadership']['title'], $content['leadership']['style']);
        $output[] = $content['leadership']['separator'];
        foreach ($content['leadership']['sections'] as $section) {
            $output[] = $this->formatOutput($section['title'], 'white');
            if (isset($section['content'])) {
                $output[] = $this->formatOutput($section['content'], 'white');
                $output[] = $this->lineBreak();
            } elseif (isset($section['items'])) {
                foreach ($section['items'] as $item) {
                    $output[] = $this->formatOutput($item, 'white');
                }
                $output[] = $this->lineBreak();
            }
        }

        return $output;
    }
}