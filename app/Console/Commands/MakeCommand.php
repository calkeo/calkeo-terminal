<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeCommand extends Command
{
    protected $signature = 'make:command {name : The name of the command} {--interactive : Whether the command should be interactive}';
    protected $description = 'Create a new command class';

    public function handle()
    {
        $name = $this->argument('name');
        $isInteractive = $this->option('interactive');

        // Convert the name to StudlyCase
        $className = Str::studly($name) . 'Command';

        // Get the path to the commands directory
        $path = app_path('Commands/' . $className . '.php');

        // Check if the file already exists
        if (File::exists($path)) {
            $this->error("Command {$className} already exists!");
            return 1;
        }

        // Create the command class
        $stub = $this->getStub($isInteractive);
        $stub = $this->replaceClassName($stub, $className);
        $stub = $this->replaceCommandName($stub, $name);

        // Write the file
        File::put($path, $stub);

        $this->info("Command {$className} created successfully!");
        return 0;
    }

    protected function getStub(bool $isInteractive): string
    {
        $stubPath = $isInteractive ? 'interactive-command.stub' : 'command.stub';
        return File::get(__DIR__ . '/stubs/' . $stubPath);
    }

    protected function replaceClassName(string $stub, string $className): string
    {
        return str_replace('{{ className }}', $className, $stub);
    }

    protected function replaceCommandName(string $stub, string $name): string
    {
        return str_replace('{{ commandName }}', $name, $stub);
    }
}
