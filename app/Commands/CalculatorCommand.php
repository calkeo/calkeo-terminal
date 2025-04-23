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
        // Split the expression into tokens
        $tokens = $this->tokenize($expression);

        // Convert infix notation to postfix (Reverse Polish Notation)
        $postfix = $this->infixToPostfix($tokens);

        // Evaluate the postfix expression
        return $this->evaluatePostfix($postfix);
    }

    /**
     * Tokenize the expression into numbers and operators
     *
     * @param  string  $expression
     * @return array
     */
    private function tokenize(string $expression): array
    {
        $tokens = [];
        $number = '';

        for ($i = 0; $i < strlen($expression); $i++) {
            $char = $expression[$i];

            if (is_numeric($char) || $char === '.') {
                $number .= $char;
            } else {
                if ($number !== '') {
                    $tokens[] = $number;
                    $number = '';
                }
                $tokens[] = $char;
            }
        }

        if ($number !== '') {
            $tokens[] = $number;
        }

        return $tokens;
    }

    /**
     * Convert infix notation to postfix (Reverse Polish Notation)
     *
     * @param  array   $tokens
     * @return array
     */
    private function infixToPostfix(array $tokens): array
    {
        $output = [];
        $operators = [];

        $precedence = [
            '+' => 1,
            '-' => 1,
            '*' => 2,
            '/' => 2,
            '%' => 2,
        ];

        foreach ($tokens as $token) {
            if (is_numeric($token) || $token === '.') {
                $output[] = $token;
            } elseif ($token === '(') {
                $operators[] = $token;
            } elseif ($token === ')') {
                while (!empty($operators) && end($operators) !== '(') {
                    $output[] = array_pop($operators);
                }

                if (!empty($operators) && end($operators) === '(') {
                    array_pop($operators); // Remove the '('
                }
            } else {
                while (!empty($operators) &&
                    end($operators) !== '(' &&
                    $precedence[end($operators)] >= $precedence[$token]) {
                    $output[] = array_pop($operators);
                }
                $operators[] = $token;
            }
        }

        while (!empty($operators)) {
            $output[] = array_pop($operators);
        }

        return $output;
    }

    /**
     * Evaluate a postfix expression
     *
     * @param  array        $postfix
     * @throws \Exception
     * @return float|int
     */
    private function evaluatePostfix(array $postfix)
    {
        $stack = [];

        foreach ($postfix as $token) {
            if (is_numeric($token) || $token === '.') {
                $stack[] = $token;
            } else {
                if (count($stack) < 2) {
                    throw new \Exception("Invalid expression");
                }

                $b = array_pop($stack);
                $a = array_pop($stack);

                switch ($token) {
                    case '+':
                        $stack[] = $a + $b;
                        break;
                    case '-':
                        $stack[] = $a - $b;
                        break;
                    case '*':
                        $stack[] = $a * $b;
                        break;
                    case '/':
                        if ($b == 0) {
                            throw new \Exception("Division by zero");
                        }
                        $stack[] = $a / $b;
                        break;
                    case '%':
                        if ($b == 0) {
                            throw new \Exception("Modulo by zero");
                        }
                        $stack[] = $a % $b;
                        break;
                    default:
                        throw new \Exception("Unknown operator: $token");
                }
            }
        }

        if (count($stack) !== 1) {
            throw new \Exception("Invalid expression");
        }

        $result = $stack[0];

        // Format the result
        if (is_float($result)) {
            // Round to 6 decimal places
            $result = round($result, 6);
            // Remove trailing zeros
            $result = rtrim(rtrim($result, '0'), '.');
        }

        return $result;
    }
}
