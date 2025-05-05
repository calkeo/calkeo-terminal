<?php

namespace App\Console\Commands;

use App\Commands\CommandRegistry;
use Illuminate\Console\Command;

class ClearCommandRegistryCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clear-command-registry-cache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear the command registry cache';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing command registry cache...');
        CommandRegistry::staticClearCache();
        $this->info('Command registry cache cleared successfully.');
    }
}
