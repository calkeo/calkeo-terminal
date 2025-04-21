<?php

namespace App\Commands;

class MySpecialCommand!Command extends AbstractCommand
{
    protected $name = 'my-special-command!';
    protected $description = 'Description of the command';

    public function execute(array $args = []): array
    {
        $output = [];

        // Add your command logic here

        return $output;
    }
}
