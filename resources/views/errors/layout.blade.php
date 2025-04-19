<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=jetbrains-mono:400,500,600" rel="stylesheet" />

        <!-- Styles -->
        <style>
            html,
            body {
                background-color: #000;
                color: #a0aec0;
                font-family: 'JetBrains Mono', ui-sans-serif, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .content {
                text-align: center;
                background-color: #111;
                border: 1px solid #333;
                border-radius: 5px;
                padding: 2rem;
                max-width: 600px;
                width: 90%;
            }

            .title {
                font-size: 36px;
                padding: 20px;
                color: #10b981;
                /* green-500 */
            }

            .error-code {
                font-size: 72px;
                font-weight: bold;
                color: #06b6d4;
                /* cyan-500 */
                margin-bottom: 1rem;
            }

            .error-message {
                font-size: 24px;
                color: #eab308;
                /* yellow-500 */
                /* margin-bottom: 2rem; */
            }

            .back-button {
                display: inline-block;
                background-color: #1e293b;
                /* slate-800 */
                color: #10b981;
                /* green-500 */
                padding: 0.75rem 1.5rem;
                border-radius: 0.25rem;
                text-decoration: none;
                border: 1px solid #334155;
                /* slate-700 */
                transition: all 0.2s;
            }

            .back-button:hover {
                background-color: #334155;
                /* slate-700 */
                border-color: #475569;
                /* slate-600 */
            }

            .terminal-header {
                background-color: #111;
                border-bottom: 1px solid #333;
                padding: 0.5rem 1rem;
                display: flex;
                align-items: center;
                border-top-left-radius: 5px;
                border-top-right-radius: 5px;
            }

            .terminal-title {
                color: #06b6d4;
                /* cyan-500 */
                font-size: 0.875rem;
            }

            .terminal-content {
                padding: 1.5rem;
            }
        </style>
    </head>

    <body>
        <div class="flex-center position-ref full-height">
            <div class="content">
                <div class="terminal-header">
                    <div class="terminal-title">calkeo.dev</div>
                </div>
                <div class="terminal-content">
                    <div class="error-code">@yield('code')</div>
                    <div class="error-message">@yield('message')</div>
                    {{-- <a href="{{ url('/terminal') }}" class="back-button">Return to Home</a> --}}
                </div>
            </div>
        </div>
    </body>

</html>