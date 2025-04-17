<?php

namespace App\Providers;

use App\Commands\AboutCommand;
use App\Commands\CalculatorCommand;
use App\Commands\ChangelogCommand;
use App\Commands\ClearCommand;
use App\Commands\CommandRegistry;
use App\Commands\ContactCommand;
use App\Commands\DateCommand;
use App\Commands\EchoCommand;
use App\Commands\HelpCommand;
use App\Commands\HistoryCommand;
use App\Commands\LogoutCommand;
use App\Commands\SudoCommand;
use App\Commands\WhoamiCommand;
use Illuminate\Support\ServiceProvider;

class CommandServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(CommandRegistry::class, function ($app) {
            $registry = new CommandRegistry();

            // Register commands
            $registry->register(new HelpCommand($registry));
            $registry->register(new ClearCommand());
            $registry->register(new AboutCommand());
            $registry->register(new LogoutCommand());
            $registry->register(new WhoamiCommand());
            $registry->register(new SudoCommand());
            $registry->register(new HistoryCommand());
            $registry->register(new EchoCommand());
            $registry->register(new DateCommand());
            $registry->register(new CalculatorCommand());
            $registry->register(new ContactCommand());
            $registry->register(new ChangelogCommand());

            // Add more commands here

            return $registry;
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
