<?php

namespace Tests\Unit\Commands;

use App\Commands\GamesCommand;
use App\Livewire\Terminal;
use Tests\TestCase;

class GamesCommandTest extends TestCase
{
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();
        $this->terminal = new Terminal();
    }

    public function test_games_command_returns_list_of_games()
    {
        $command = new GamesCommand();
        $output = $command->execute($this->terminal);

        $this->assertNotEmpty($output);
        $this->assertStringContainsString('Available Games', $output[0]);
    }

    public function test_games_are_sorted_alphabetically()
    {
        $command = new GamesCommand();
        $games = $command->games();

        $sortedGames = $games->sortBy('name');

        $this->assertEquals($games, $sortedGames);
    }

    public function test_games_collection_has_sequential_indexes()
    {
        $command = new GamesCommand();
        $games = $command->games();

        $this->assertEquals(range(0, count($games) - 1), $games->keys()->toArray());
    }

    public function test_number_guessing_game_is_included()
    {
        $command = new GamesCommand();
        $games = $command->games();

        $this->assertTrue($games->contains('name', 'Number Guessing'));
    }
}
