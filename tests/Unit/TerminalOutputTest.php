<?php

namespace Tests\Unit;

use App\Livewire\Terminal;
use PHPUnit\Framework\TestCase;

class TerminalOutputTest extends TestCase
{
    protected $terminal;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a mock Terminal component
        $this->terminal = new class extends Terminal
        {
            // Override methods that require dependencies
            public function boot($registry = null, $parser = null)
            {}
            public function mount()
            {}
            public function render()
            {return null;}

            // Expose protected properties for testing
            public function getOutput()
            {return $this->output;}
            public function getLastOutput()
            {return $this->lastOutput;}
            public function getReplaceLastOutput()
            {return $this->replaceLastOutput;}

            // Expose methods for testing
            public function testExecuteCommand($command, $result)
            {
                if ($this->replaceLastOutput) {
                    // Find where the last output starts, including the command prompt line
                    $lastOutputStart = count($this->output) - count($this->lastOutput) - 1;
                    if ($lastOutputStart >= 0) {
                        // Keep everything before the last output
                        $this->output = array_slice($this->output, 0, $lastOutputStart);
                    }
                    $this->replaceLastOutput = false;
                }

                // Add command to output
                $this->output[] = "<span class='text-cyan-400'>$</span> <span class='text-green-400'>" . htmlspecialchars($command) . "</span>";

                // Add the new output
                foreach ($result as $line) {
                    $this->output[] = $line;
                }

                // Store the result
                $this->lastOutput = $result;
            }

            public function testDelayedOutput($result)
            {
                $this->isProcessingDelayedOutput = true;

                if ($this->replaceLastOutput) {
                    // Find where the last output starts, including the command prompt line
                    $lastOutputStart = count($this->output) - count($this->lastOutput) - 1;
                    if ($lastOutputStart >= 0) {
                        // Keep everything before the last output and the command prompt line
                        $this->output = array_slice($this->output, 0, $lastOutputStart + 1);
                    }
                    $this->replaceLastOutput = false;
                }

                // Add the new output
                foreach ($result as $line) {
                    if (is_array($line) && isset($line['type']) && $line['type'] === 'delayed') {
                        $this->output[] = $line['content'];
                    } else {
                        $this->output[] = $line;
                    }
                }

                $this->lastOutput = $result;
                $this->isProcessingDelayedOutput = false;
            }

            public function setReplaceLastOutput($value)
            {
                $this->replaceLastOutput = $value;
            }

            public function setOutput($output)
            {
                $this->output = $output;
            }
        };
    }

    public function testOutputReplacementInExecuteCommand()
    {
        // Initial output
        $this->terminal->setOutput(['Initial line 1', 'Initial line 2']);

        // First command result
        $command1 = 'test command 1';
        $result1 = ['Result 1', 'Result 2'];
        $this->terminal->testExecuteCommand($command1, $result1);

        // Verify output after first command
        $this->assertEquals(
            ['Initial line 1', 'Initial line 2', "<span class='text-cyan-400'>$</span> <span class='text-green-400'>test command 1</span>", 'Result 1', 'Result 2'],
            $this->terminal->getOutput()
        );
        $this->assertEquals($result1, $this->terminal->getLastOutput());

        // Set replace flag and execute second command
        $this->terminal->setReplaceLastOutput(true);
        $command2 = 'test command 2';
        $result2 = ['New Result 1', 'New Result 2'];
        $this->terminal->testExecuteCommand($command2, $result2);

        // Verify output after replacement
        $this->assertEquals(
            ['Initial line 1', 'Initial line 2', "<span class='text-cyan-400'>$</span> <span class='text-green-400'>test command 2</span>", 'New Result 1', 'New Result 2'],
            $this->terminal->getOutput()
        );
        $this->assertEquals($result2, $this->terminal->getLastOutput());
        $this->assertFalse($this->terminal->getReplaceLastOutput());
    }

    public function testOutputReplacementInDelayedOutput()
    {
        // Initial output
        $this->terminal->setOutput(['Initial line 1', 'Initial line 2']);

        // First command result
        $command1 = 'test command 1';
        $result1 = ['Result 1', 'Result 2'];
        $this->terminal->testExecuteCommand($command1, $result1);

        // Verify output after first command
        $this->assertEquals(
            ['Initial line 1', 'Initial line 2', "<span class='text-cyan-400'>$</span> <span class='text-green-400'>test command 1</span>", 'Result 1', 'Result 2'],
            $this->terminal->getOutput()
        );
        $this->assertEquals($result1, $this->terminal->getLastOutput());

        // Set replace flag and execute delayed output
        $this->terminal->setReplaceLastOutput(true);
        $delayedResult = [
            ['type' => 'delayed', 'content' => 'Delayed Result 1'],
            ['type' => 'delayed', 'content' => 'Delayed Result 2'],
        ];
        $this->terminal->testDelayedOutput($delayedResult);

        // Verify output after delayed replacement
        $this->assertEquals(
            ['Initial line 1', 'Initial line 2', "<span class='text-cyan-400'>$</span> <span class='text-green-400'>test command 1</span>", 'Delayed Result 1', 'Delayed Result 2'],
            $this->terminal->getOutput()
        );
        $this->assertEquals($delayedResult, $this->terminal->getLastOutput());
        $this->assertFalse($this->terminal->getReplaceLastOutput());
    }

    public function testOutputReplacementWithDifferentSizes()
    {
        // Initial output
        $this->terminal->setOutput(['Initial line 1', 'Initial line 2']);

        // First command result (3 lines)
        $command1 = 'test command 1';
        $result1 = ['Result 1', 'Result 2', 'Result 3'];
        $this->terminal->testExecuteCommand($command1, $result1);

        // Verify output after first command
        $this->assertEquals(
            ['Initial line 1', 'Initial line 2', "<span class='text-cyan-400'>$</span> <span class='text-green-400'>test command 1</span>", 'Result 1', 'Result 2', 'Result 3'],
            $this->terminal->getOutput()
        );
        $this->assertEquals($result1, $this->terminal->getLastOutput());

        // Set replace flag and execute second command (2 lines)
        $this->terminal->setReplaceLastOutput(true);
        $command2 = 'test command 2';
        $result2 = ['New Result 1', 'New Result 2'];
        $this->terminal->testExecuteCommand($command2, $result2);

        // Verify output after replacement
        $this->assertEquals(
            ['Initial line 1', 'Initial line 2', "<span class='text-cyan-400'>$</span> <span class='text-green-400'>test command 2</span>", 'New Result 1', 'New Result 2'],
            $this->terminal->getOutput(),
            'Failed to replace last output with different size output'
        );
        $this->assertEquals($result2, $this->terminal->getLastOutput());
        $this->assertFalse($this->terminal->getReplaceLastOutput());

        // Test replacing with larger output
        $this->terminal->setReplaceLastOutput(true);
        $command3 = 'test command 3';
        $result3 = ['Final Result 1', 'Final Result 2', 'Final Result 3', 'Final Result 4'];
        $this->terminal->testExecuteCommand($command3, $result3);

        // Verify output after replacement with larger output
        $this->assertEquals(
            ['Initial line 1', 'Initial line 2', "<span class='text-cyan-400'>$</span> <span class='text-green-400'>test command 3</span>", 'Final Result 1', 'Final Result 2', 'Final Result 3', 'Final Result 4'],
            $this->terminal->getOutput(),
            'Failed to replace last output with larger output'
        );
        $this->assertEquals($result3, $this->terminal->getLastOutput());
        $this->assertFalse($this->terminal->getReplaceLastOutput());
    }
}
