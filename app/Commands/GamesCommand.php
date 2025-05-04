<?php

namespace App\Commands;

use App\Commands\ChessCommand;
use App\Commands\TicTacToeCommand;
use App\Livewire\Terminal;
use Illuminate\Support\Collection;

class GamesCommand extends AbstractCommand
{
    public function games(): Collection
    {
        $games = collect([
            [
                'name' => 'Global Thermonuclear War',
                'description' => 'A game of strategy and deception',
                'command' => new GlobalThermonuclearWarCommand(),
            ],
            [
                'name' => 'Chess',
                'description' => 'The classic game of kings',
                'command' => new ChessCommand(),
            ],
            [
                'name' => 'Tic-Tac-Toe',
                'description' => 'Simple but challenging',
                'command' => new TicTacToeCommand(),
            ],
            [
                'name' => 'Hangman',
                'description' => 'Test your vocabulary',
            ],
            [
                'name' => 'Number Guessing',
                'description' => 'Can you guess the number?',
                'command' => new NumberGuessingCommand(),
            ],
            [
                'name' => 'Rock, Paper, Scissors',
                'description' => 'The timeless decision maker',
                'command' => new RockPaperScissorsCommand(),
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
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        $output = [];

        $output[] = $this->formatOutput('Available Games', 'header');
        $output[] = '===============';
        $output[] = '';

        foreach ($this->games() as $key => $game) {
            $output[] = $this->formatOutput($key + 1 . '. ' . $game['name'], 'subheader');
            $output[] = $this->formatOutput($game['description'], 'white');
            if (isset($game['command'])) {
                $output[] = $this->formatOutput('Command: ' . $game['command']->getName(), 'command');
            } else {
                $output[] = $this->formatOutput('Coming soon...', 'normal');
            }
            $output[] = $this->formatOutput('--------------------------------', 'normal');
        }

        return $output;
    }
}
