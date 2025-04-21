@extends('satire.layouts.app')

@section('title', 'Documentation')

@section('content')
<div class="bg-white shadow rounded-lg overflow-hidden">
    <div class="px-4 py-5 sm:px-6">
        <h1 class="text-2xl font-bold text-gray-900">calkeOS Documentation</h1>
        <p class="mt-1 text-sm text-gray-500">Everything you need to know about our revolutionary terminal interface</p>
    </div>

    <div class="border-t border-gray-200">
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Version</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">{{ config('app.version') }} (Stable)</div>
        </div>
        <div class="bg-white px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Last Updated</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Never (We're too busy coding)</div>
        </div>
        <div class="bg-gray-50 px-4 py-5 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-6">
            <div class="text-sm font-medium text-gray-500">Documentation Status</div>
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">Incomplete (Like all good documentation)</div>
        </div>
    </div>

    <div class="px-4 py-5 sm:p-6">
        <div class="prose max-w-none">
            <h2 class="text-xl font-semibold text-gray-900">Introduction</h2>
            <p class="mt-2 text-gray-700">
                Welcome to the calkeOS Terminal, the most advanced command-line interface ever created (in our dreams).
                This documentation will guide you through the basics of using our terminal, or at least what we remember
                about
                it.
            </p>

            <div class="terminal-window my-6">
                <div class="terminal-header">
                    <div class="terminal-button" style="background-color: #ff5f56;"></div>
                    <div class="terminal-button" style="background-color: #ffbd2e;"></div>
                    <div class="terminal-button" style="background-color: #27c93f;"></div>
                </div>
                <div class="terminal-content">
                    <div class="terminal-prompt">$</div>
                    <div class="terminal-command">welcome --to calkeo-terminal</div>
                    <div class="terminal-output">
                        Welcome to calkeOS {{ config('app.version') }}!
                        Type 'help' to see available commands or 'exit' to leave (if you can find it).
                    </div>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mt-8">Getting Started</h2>
            <p class="mt-2 text-gray-700">
                To get started with calkeOS, simply open your browser and navigate to our application.
                No installation required! (Because we're too lazy to create installers)
            </p>

            <div class="terminal-warning-box my-4">
                <p class="text-gray-700">
                    <strong>Warning:</strong> This terminal is for entertainment purposes only.
                    Do not use it for any actual work unless you enjoy debugging for hours on end.
                </p>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mt-8">Troubleshooting</h2>
            <p class="mt-2 text-gray-700">
                If you encounter any issues with calkeOS, here are some troubleshooting steps:
            </p>

            <ol class="list-decimal pl-5 mt-2 text-gray-700">
                <li>Have you tried turning it off and on again?</li>
                <li>Is it plugged in?</li>
                <li>Have you tried Googling it?</li>
                <li>Have you tried blaming the intern?</li>
                <li>Have you tried sacrificing a goat to the tech gods?</li>
                <li>Have you tried becoming a farmer instead?</li>
            </ol>

            <div class="terminal-error-box my-4">
                <p class="text-gray-700">
                    <strong>Error:</strong> If none of these steps work, the problem is probably in the backlog and will
                    be fixed
                    in a future update (sometime between now and the heat death of the universe).
                </p>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mt-8">Conclusion</h2>
            <p class="mt-2 text-gray-700">
                Congratulations! You've reached the end of our documentation. If you have any questions, please refer to
                our
                support page,
                where our highly trained support team will tell you to turn it off and on again.
            </p>

            <div class="terminal-note my-4">
                <p class="text-gray-700">
                    <strong>Note:</strong> This documentation was written by developers who were running on pure
                    caffeine and
                    determination.
                    No documentation was harmed in the making of this terminal.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('footer_text')
Documentation last updated: Never (We're too busy coding)
@endsection

@section('scripts')
<script>
    // Add some interactivity to the documentation
    document.addEventListener('DOMContentLoaded', function() {
        // Add a blinking cursor to the terminal
        const terminals = document.querySelectorAll('.terminal-content');
        terminals.forEach(terminal => {
            const cursor = document.createElement('div');
            cursor.className = 'terminal-prompt';
            cursor.innerHTML = '_';
            cursor.style.animation = 'blink 1s infinite';
            terminal.appendChild(cursor);
        });
    });
</script>
@endsection