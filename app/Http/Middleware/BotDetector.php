<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Jaybizzle\CrawlerDetect\CrawlerDetect;
use Symfony\Component\HttpFoundation\Response;

class BotDetector
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure                 $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (config('app.test_bot_views') || Session::get('view_plain_text')) {
            $request->attributes->add(['is_bot' => true]);
            return $next($request);
        }

        $CrawlerDetect = new CrawlerDetect;

        $userAgent = $request->header('User-Agent');

        if ($userAgent && $CrawlerDetect->isCrawler($userAgent)) {
            // Set a flag in the request to indicate this is a bot
            $request->attributes->add(['is_bot' => true]);
        } else {
            $request->attributes->add(['is_bot' => false]);
        }

        return $next($request);
    }
}
