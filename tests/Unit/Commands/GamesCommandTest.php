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

        // Check that we have usage instructions - using a more flexible approach
        $hasUsage = false;
        foreach ($output as $line) {
            if (str_contains($line, 'Usage:')) {
                $hasUsage = true;
                break;
            }
        }
        $this->assertTrue($hasUsage, 'Output should contain "Usage:" text');

        // Check example section - using a more flexible approach
        $hasExample = false;
        foreach ($output as $line) {
            if (str_contains($line, 'Example:')) {
                $hasExample = true;
                break;
            }
        }
        $this->assertTrue($hasExample, 'Output should contain "Example:" text');
    }

    public function test_games_command_returns_global_thermonuclear_war_game()
    {
        // Find the index of Global Thermonuclear War in the games list
        $games = $this->command->games();
        $globalThermonuclearWarIndex = $games->search(function ($game) {
            return $game['name'] === 'Global Thermonuclear War';
        }) + 1;

        $output = $this->command->execute([$globalThermonuclearWarIndex]);

        // Check that we have the expected output for Global Thermonuclear War
        $this->assertEquals('delayed', $output[0]['type']);
        $this->assertEquals(0, $output[0]['delay']);
        $this->assertStringContainsString('Global Thermonuclear War', $output[0]['content']);

        $this->assertEquals('delayed', $output[1]['type']);
        $this->assertEquals(0, $output[1]['delay']);
        $this->assertStringContainsString('=========================', $output[1]['content']);

        $this->assertEquals('delayed', $output[2]['type']);
        $this->assertEquals(1500, $output[2]['delay']);
        $this->assertStringContainsString('Greetings Professor Falken.', $output[2]['content']);

        $this->assertEquals('delayed', $output[3]['type']);
        $this->assertEquals(3000, $output[3]['delay']);
        $this->assertStringContainsString('A strange game. The only winning move is not to play.', $output[3]['content']);

        $this->assertEquals('delayed', $output[4]['type']);
        $this->assertEquals(3000, $output[4]['delay']);
        $this->assertStringContainsString('How about a nice game of chess?', $output[4]['content']);
    }

    public function test_games_command_returns_error_for_invalid_game()
    {
        // Mock the games method to return an empty collection
        $this->command = $this->getMockBuilder(GamesCommand::class)
                              ->onlyMethods(['games'])
                              ->getMock();

        $this->command->method('games')
             ->willReturn(collect([]));

        // Use an invalid game index
        $output = $this->command->execute([1]);

        // Check that we have the expected error message
        $this->assertStringContainsString('Game not found', $output[0]);
    }

    public function test_games_command_returns_not_available_message_for_other_games()
    {
        // Find the index of Chess in the games list
        $games = $this->command->games();
        $chessIndex = $games->search(function ($game) {
            return $game['name'] === 'Chess';
        }) + 1;

        $output = $this->command->execute([$chessIndex]);

        // Check that we have the expected not available message
        $this->assertStringContainsString('This game is not available yet', $output[0]);
        $this->assertStringContainsString('Please try again later', $output[1]);
    }

    public function test_games_are_sorted_alphabetically()
    {
        $games = $this->command->games();

        // Check that the games are sorted alphabetically by name
        $names = $games->pluck('name')->toArray();
        $sortedNames = $names;
        sort($sortedNames);

        $this->assertEquals($sortedNames, $names);
    }

    public function test_games_collection_has_sequential_indexes()
    {
        $games = $this->command->games();

        // Check that the collection has sequential numeric indexes
        $keys = $games->keys()->toArray();
        $expectedKeys = range(0, count($games) - 1);

        $this->assertEquals($expectedKeys, $keys);
    }
}
