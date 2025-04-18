<?php

namespace App\Commands;

use Illuminate\Support\Collection;

class GamesCommand extends AbstractCommand
{
    public function games(): Collection
    {
        $games = collect([
            [
                'name' => 'Global Thermonuclear War',
                'description' => 'A game of strategy and deception',
            ],
            [
                'name' => 'Chess',
                'description' => 'The classic game of kings',
            ],
            [
                'name' => 'Tic-Tac-Toe',
                'description' => 'Simple but challenging',
            ],
            [
                'name' => 'Hangman',
                'description' => 'Test your vocabulary',
            ],
            [
                'name' => 'Number Guessing',
                'description' => 'Can you guess the number?',
            ],
            [
                'name' => 'Rock, Paper, Scissors',
                'description' => 'The timeless decision maker',
            ],
            [
                'name' => 'Battleship',
                'description' => 'Naval warfare strategy',
            ],
            [
                'name' => 'Connect Four',
                'description' => 'Line up four in a row',
            ],
        ]);

        return $games->sortBy('name')->values();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->name = 'games';
        $this->description = 'List available games';
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

        if (count($args) > 0) {
            $gameName = $args[0];
            $games = $this->games();

            // Check if the game index is valid
            if (!isset($games[$args[0] - 1])) {
                return [
                    $this->formatOutput('Game not found', 'error'),
                ];
            }

            $game = $games[$args[0] - 1];

            if ($game['name'] === 'Global Thermonuclear War') {
                return $this->globalThermonuclearWar();
            } else {
                return [
                    $this->formatOutput('This game is not available yet', 'error'),
                    $this->formatOutput('Please try again later', 'error'),
                ];
            }
        }

        $output[] = $this->formatOutput('Available Games', 'header');
        $output[] = '===============';
        $output[] = '';

        foreach ($this->games() as $key => $game) {
            $output[] = $this->formatOutput($key + 1 . '. ' . $game['name'], 'subheader');
            $output[] = $this->formatOutput($game['description'], 'normal');
            $output[] = $this->formatOutput('--------------------------------', 'normal');
        }

        $output[] = '';
        $output[] = $this->formatOutput('Usage:', 'header');
        $output[] = $this->formatOutput('games &lt;game number&gt;', 'command');
        $output[] = $this->formatOutput('', 'command');
        $output[] = $this->formatOutput('Example:', 'header');
        $rockPaperScissorsIndex = collect($this->games())->search(function ($game) {
            return $game['name'] === 'Rock, Paper, Scissors';
        }) + 1;
        $output[] = $this->formatOutput('To play Rock, Paper, Scissors:', 'command');
        $output[] = $this->formatOutput("games {$rockPaperScissorsIndex}", 'command');

        return $output;
    }

    private function globalThermonuclearWar(): array
    {
        $output = [];

        $output[] = [
            'type' => 'delayed',
            'delay' => 0,
            'content' => $this->formatOutput('Global Thermonuclear War', 'subheader'),
        ];
        $output[] = [
            'type' => 'delayed',
            'delay' => 0,
            'content' => $this->formatOutput('=========================', 'subheader'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 1500,
            'content' => $this->formatOutput("Greetings Professor Falken.", 'white'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 3000,
            'content' => $this->formatOutput("A strange game. The only winning move is not to play.", 'white'),
        ];

        $output[] = [
            'type' => 'delayed',
            'delay' => 3000,
            'content' => $this->formatOutput("How about a nice game of chess?", 'success'),
        ];

        return $output;
    }
}