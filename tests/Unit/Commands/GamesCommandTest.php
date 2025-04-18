<?php

namespace Tests\Unit\Commands;

use App\Commands\GamesCommand;
use Tests\TestCase;

class GamesCommandTest extends TestCase
{
    protected $command;

    protected function setUp(): void
    {
        parent::setUp();
        $this->command = new GamesCommand();
    }

    public function test_games_command_returns_list_of_games()
    {
        $output = $this->command->execute();

        // Check that we have the expected header
        $this->assertStringContainsString('Available Games', $output[0]);

        // Check separator line
        $this->assertEquals('===============', $output[1]);

        // Check empty line
        $this->assertEquals('', $output[2]);

        // Check that we have game entries
        $this->assertGreaterThan(3, count($output));
    }

    public function test_games_are_sorted_alphabetically()
    {
        $games = $this->command->games();
        $gameNames = $games->pluck('name')->toArray();
        $sortedGameNames = $gameNames;
        sort($sortedGameNames);

        $this->assertEquals($sortedGameNames, $gameNames);
    }

    public function test_games_collection_has_sequential_indexes()
    {
        $games = $this->command->games();
        $indexes = $games->keys()->toArray();
        $expectedIndexes = range(0, count($games) - 1);

        $this->assertEquals($expectedIndexes, $indexes);
    }
}