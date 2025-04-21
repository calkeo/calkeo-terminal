<?php

namespace App\Providers;

use App\Http\Middleware\BotDetector;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class BotDetectorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Register the middleware globally
        $this->app['router']->aliasMiddleware('bot.detector', BotDetector::class);

        // Apply the middleware to all routes
        $this->app['router']->pushMiddlewareToGroup('web', BotDetector::class);
    }
}
