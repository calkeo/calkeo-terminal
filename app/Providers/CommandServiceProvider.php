<?php

namespace App\Providers;

use App\Commands\AboutCommand;
use App\Commands\CalculatorCommand;
use App\Commands\ChangelogCommand;
use App\Commands\ChessCommand;
use App\Commands\ClearCommand;
use App\Commands\CommandRegistry;
use App\Commands\ConnectFourCommand;
use App\Commands\ContactCommand;
use App\Commands\DateCommand;
use App\Commands\EchoCommand;
use App\Commands\ForbiddenCommand;
use App\Commands\GamesCommand;
use App\Commands\GithubCommand;
use App\Commands\GlobalThermonuclearWarCommand;
use App\Commands\HangmanCommand;
use App\Commands\HelpCommand;
use App\Commands\HistoryCommand;
use App\Commands\LogoutCommand;
use App\Commands\NumberGuessingCommand;
use App\Commands\PrivacyCommand;
use App\Commands\RockPaperScissorsCommand;
use App\Commands\SshCommand;
use App\Commands\SudoCommand;
use App\Commands\TicTacToeCommand;
use App\Commands\VersionCommand;
use App\Commands\WhoamiCommand;
use App\Commands\WhoisCommand;
use App\Commands\WordChainCommand;
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

            if ($registry->commands->isNotEmpty() && $registry->aliases->isNotEmpty()) {
                return $registry;
            }

            // Clear cache when registering new commands
            $registry->clearCache();

            // Core commands
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
            $registry->register(new GithubCommand());
            $registry->register(new VersionCommand());
            $registry->register(new PrivacyCommand());
            $registry->register(new WhoisCommand());
            $registry->register(new SshCommand());

            // Game commands
            $registry->register(new GamesCommand());
            $registry->register(new RockPaperScissorsCommand());
            $registry->register(new GlobalThermonuclearWarCommand());
            $registry->register(new NumberGuessingCommand());
            $registry->register(new TicTacToeCommand());
            $registry->register(new ChessCommand());
            $registry->register(new HangmanCommand());
            $registry->register(new WordChainCommand());
            $registry->register(new ConnectFourCommand());
            $registry->register(new ForbiddenCommand());

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
