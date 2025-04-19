<?php

/**
 * Sentry Laravel SDK configuration file.
 *
 * @see https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/
 */
return [

    // @see https://docs.sentry.io/product/sentry-basics/dsn-explainer/
    'dsn' => app()->environment('production') ? env('SENTRY_LARAVEL_DSN', env('SENTRY_DSN')) : null,

    // @see https://spotlightjs.com/
    // 'spotlight' => env('SENTRY_SPOTLIGHT', false),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#logger
    // 'logger' => Sentry\Logger\DebugFileLogger::class, // By default this will log to `storage_path('logs/sentry.log')`

    // The release version of your application
    // Example with dynamic git hash: trim(exec('git --git-dir ' . base_path('.git') . ' log --pretty="%h" -n1 HEAD'))
    'release' => env('SENTRY_RELEASE'),

    // When left empty or `null` the Laravel environment will be used (usually discovered from `APP_ENV` in your `.env`)
    'environment' => env('SENTRY_ENVIRONMENT'),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#sample-rate
    'sample_rate' => app()->environment('production') ? (env('SENTRY_SAMPLE_RATE') === null ? 1.0 : (float) env('SENTRY_SAMPLE_RATE')) : 0.0,

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#traces-sample-rate
    'traces_sample_rate' => app()->environment('production') ? (env('SENTRY_TRACES_SAMPLE_RATE') === null ? null : (float) env('SENTRY_TRACES_SAMPLE_RATE')) : 0.0,

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#profiles-sample-rate
    'profiles_sample_rate' => app()->environment('production') ? (env('SENTRY_PROFILES_SAMPLE_RATE') === null ? null : (float) env('SENTRY_PROFILES_SAMPLE_RATE')) : 0.0,

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#send-default-pii
    'send_default_pii' => env('SENTRY_SEND_DEFAULT_PII', false),

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#ignore-exceptions
    // 'ignore_exceptions' => [],

    // @see: https://docs.sentry.io/platforms/php/guides/laravel/configuration/options/#ignore-transactions
    'ignore_transactions' => [
        // Ignore Laravel's default health URL
        '/up',
    ],

    // Breadcrumb specific configuration
    'breadcrumbs' => [
        // Capture Laravel logs as breadcrumbs
        'logs' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_LOGS_ENABLED', true) : false,

        // Capture Laravel cache events (hits, writes etc.) as breadcrumbs
        'cache' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_CACHE_ENABLED', true) : false,

        // Capture Livewire components like routes as breadcrumbs
        'livewire' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_LIVEWIRE_ENABLED', true) : false,

        // Capture SQL queries as breadcrumbs
        'sql_queries' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_SQL_QUERIES_ENABLED', true) : false,

        // Capture SQL query bindings (parameters) in SQL query breadcrumbs
        'sql_bindings' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_SQL_BINDINGS_ENABLED', false) : false,

        // Capture queue job information as breadcrumbs
        'queue_info' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_QUEUE_INFO_ENABLED', true) : false,

        // Capture command information as breadcrumbs
        'command_info' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_COMMAND_JOBS_ENABLED', true) : false,

        // Capture HTTP client request information as breadcrumbs
        'http_client_requests' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_HTTP_CLIENT_REQUESTS_ENABLED', true) : false,

        // Capture send notifications as breadcrumbs
        'notifications' => app()->environment('production') ? env('SENTRY_BREADCRUMBS_NOTIFICATIONS_ENABLED', true) : false,
    ],

    // Performance monitoring specific configuration
    'tracing' => [
        // Trace queue jobs as their own transactions (this enables tracing for queue jobs)
        'queue_job_transactions' => app()->environment('production') ? env('SENTRY_TRACE_QUEUE_ENABLED', true) : false,

        // Capture queue jobs as spans when executed on the sync driver
        'queue_jobs' => app()->environment('production') ? env('SENTRY_TRACE_QUEUE_JOBS_ENABLED', true) : false,

        // Capture SQL queries as spans
        'sql_queries' => app()->environment('production') ? env('SENTRY_TRACE_SQL_QUERIES_ENABLED', true) : false,

        // Capture SQL query bindings (parameters) in SQL query spans
        'sql_bindings' => app()->environment('production') ? env('SENTRY_TRACE_SQL_BINDINGS_ENABLED', false) : false,

        // Capture where the SQL query originated from on the SQL query spans
        'sql_origin' => app()->environment('production') ? env('SENTRY_TRACE_SQL_ORIGIN_ENABLED', true) : false,

        // Define a threshold in milliseconds for SQL queries to resolve their origin
        'sql_origin_threshold_ms' => env('SENTRY_TRACE_SQL_ORIGIN_THRESHOLD_MS', 100),

        // Capture views rendered as spans
        'views' => app()->environment('production') ? env('SENTRY_TRACE_VIEWS_ENABLED', true) : false,

        // Capture Livewire components as spans
        'livewire' => app()->environment('production') ? env('SENTRY_TRACE_LIVEWIRE_ENABLED', true) : false,

        // Capture HTTP client requests as spans
        'http_client_requests' => app()->environment('production') ? env('SENTRY_TRACE_HTTP_CLIENT_REQUESTS_ENABLED', true) : false,

        // Capture Laravel cache events (hits, writes etc.) as spans
        'cache' => app()->environment('production') ? env('SENTRY_TRACE_CACHE_ENABLED', true) : false,

        // Capture Redis operations as spans (this enables Redis events in Laravel)
        'redis_commands' => app()->environment('production') ? env('SENTRY_TRACE_REDIS_COMMANDS', false) : false,

        // Capture where the Redis command originated from on the Redis command spans
        'redis_origin' => app()->environment('production') ? env('SENTRY_TRACE_REDIS_ORIGIN_ENABLED', true) : false,

        // Capture send notifications as spans
        'notifications' => app()->environment('production') ? env('SENTRY_TRACE_NOTIFICATIONS_ENABLED', true) : false,

        // Enable tracing for requests without a matching route (404's)
        'missing_routes' => app()->environment('production') ? env('SENTRY_TRACE_MISSING_ROUTES_ENABLED', false) : false,

        // Configures if the performance trace should continue after the response has been sent to the user until the application terminates
        // This is required to capture any spans that are created after the response has been sent like queue jobs dispatched using `dispatch(...)->afterResponse()` for example
        'continue_after_response' => app()->environment('production') ? env('SENTRY_TRACE_CONTINUE_AFTER_RESPONSE', true) : false,

        // Enable the tracing integrations supplied by Sentry (recommended)
        'default_integrations' => app()->environment('production') ? env('SENTRY_TRACE_DEFAULT_INTEGRATIONS_ENABLED', true) : false,
    ],

];