<?php

namespace App\Commands;

use App\Livewire\Terminal;

class CalculatorCommand extends AbstractCommand
{
    protected $name = 'calc';
    protected $description = 'Perform basic arithmetic calculations';

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        if (empty($args)) {
            return [
                $this->formatOutput("Usage: calc <expression>", 'warning'),
                $this->formatOutput("Example: calc 2 + 2", 'info'),
                $this->formatOutput("Supported operations: +, -, *, /, %", 'info'),
            ];
        }

        // Join arguments to form the expression
        $expression = implode('', $args);

        try {
            // Remove all spaces
            $expression = str_replace(' ', '', $expression);

            // Basic character validation
            if (!preg_match('/^[\d\+\-\*\/\%\(\)\.]+$/', $expression)) {
                throw new \Exception("Invalid characters in expression");
            }

            // Validate parentheses
            if (substr_count($expression, '(') !== substr_count($expression, ')')) {
                throw new \Exception("Mismatched parentheses");
            }

            // Check for invalid operator sequences
            if (preg_match('/[\+\-\*\/\%]{2,}/', $expression)) {
                throw new \Exception("Invalid expression");
            }

            // Check for division by zero
            if (preg_match('/\/\s*0/', $expression)) {
                throw new \Exception("Division by zero or invalid operation");
            }

            // Check for invalid start/end
            if (preg_match('/^[\+\*\/\%]|[\+\-\*\/\%]$/', $expression)) {
                throw new \Exception("Invalid expression");
            }

            // Evaluate the expression
            $result = $this->evaluateExpression($expression);

            return [
                sprintf(
                    "%s = %s",
                    $this->formatOutput($expression, 'command'),
                    $this->formatOutput($result, 'value')
                ),
            ];
        } catch (\Exception $e) {
            return [
                $this->formatOutput("Error: " . $e->getMessage(), 'error'),
            ];
        }
    }

    /**
     * Evaluate a mathematical expression
     *
     * @param  string       $expression
     * @throws \Exception
     * @return float|int
     */
    protected function evaluateExpression(string $expression)
    {
        try {
            $result = @eval("return $expression;");

            if ($result === false && error_get_last()) {
                throw new \Exception("Invalid expression");
            }

            // Check for division by zero
            if (is_infinite($result) || is_nan($result)) {
                throw new \Exception("Division by zero or invalid operation");
            }

            // Format the result
            if (is_float($result)) {
                // Round to 6 decimal places
                $result = round($result, 6);
                // Remove trailing zeros
                $result = rtrim(rtrim($result, '0'), '.');
            }

            return $result;
        } catch (\ParseError $e) {
            throw new \Exception("Invalid expression");
        }
    }
}
