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
            <div class="mt-1 text-sm text-gray-900 sm:mt-0 sm:col-span-2">6.9.420 (Stable)</div>
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
                Welcome to the Calkeo Terminal, the most advanced command-line interface ever created (in our dreams).
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
                        Welcome to Calkeo Terminal v6.9.420!
                        Type 'help' to see available commands or 'exit' to leave (if you can find it).
                    </div>
                </div>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mt-8">Getting Started</h2>
            <p class="mt-2 text-gray-700">
                To get started with Calkeo Terminal, simply open your browser and navigate to our application.
                No installation required! (Because we're too lazy to create installers)
            </p>

            <div class="terminal-warning-box my-4">
                <p class="text-gray-700">
                    <strong>Warning:</strong> This terminal is for entertainment purposes only.
                    Do not use it for any actual work unless you enjoy debugging for hours on end.
                </p>
            </div>

            <h2 class="text-xl font-semibold text-gray-900 mt-8">Basic Commands</h2>
            <p class="mt-2 text-gray-700">
                Here are some of the basic commands you can use in the Calkeo Terminal:
            </p>

            <table class="terminal-table my-4">
                <thead>
                    <tr>
                        <th>Command</th>
                        <th>Description</th>
                        <th>Example</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><code class="terminal-highlight">help</code></td>
                        <td>Displays a list of available commands (or at least the ones we remember)</td>
                        <td><code class="terminal-command">help</code></td>
                    </tr>
                    <tr>
                        <td><code class="terminal-highlight">clear</code></td>
                        <td>Clears the terminal screen (sometimes)</td>
                        <td><code class="terminal-command">clear</code></td>
                    </tr>
                    <tr>
                        <td><code class="terminal-highlight">exit</code></td>
                        <td>Exits the terminal (if you can find it)</td>
                        <td><code class="terminal-command">exit</code></td>
                    </tr>
                    <tr>
                        <td><code class="terminal-highlight">coffee</code></td>
                        <td>Requests a coffee (virtual only)</td>
                        <td><code class="terminal-command">coffee --type espresso --shots 3</code></td>
                    </tr>
                    <tr>
                        <td><code class="terminal-highlight">motivate</code></td>
                        <td>Displays a random motivational quote (usually about coffee)</td>
                        <td><code class="terminal-command">motivate</code></td>
                    </tr>
                </tbody>
            </table>

            <h2 class="text-xl font-semibold text-gray-900 mt-8">Advanced Features</h2>
            <p class="mt-2 text-gray-700">
                The Calkeo Terminal includes several advanced features that will make your life easier (or more
                complicated):
            </p>

            <h3 class="text-lg font-medium text-gray-900 mt-4">Coffee Integration</h3>
            <p class="mt-2 text-gray-700">
                Our terminal has built-in coffee integration. Simply type <code class="terminal-highlight">coffee</code>
                followed by your preferences:
            </p>

            <div class="terminal-code my-4">
                <div class="terminal-prompt">$</div>
                <div class="terminal-command">coffee --type espresso --shots 3 --temperature hot --sugar none</div>
                <div class="terminal-output">
                    Brewing your virtual espresso...
                    <div class="terminal-success">Your virtual coffee is ready! Enjoy your imaginary caffeine boost!
                    </div>
                </div>
            </div>

            <h3 class="text-lg font-medium text-gray-900 mt-4">Motivational System</h3>
            <p class="mt-2 text-gray-700">
                Feeling down? Our terminal can provide you with random motivational quotes:
            </p>

            <div class="terminal-code my-4">
                <div class="terminal-prompt">$</div>
                <div class="terminal-command">motivate</div>
                <div class="terminal-output">
                    <div class="terminal-info">"The only bug-free code is the code that hasn't been written yet."</div>
                    <div class="terminal-info">"Coffee: Because adulting is hard."</div>
                    <div class="terminal-info">"If at first you don't succeed, try turning it off and on again."</div>
                </div>
            </div>

            <h3 class="text-lg font-medium text-gray-900 mt-4">Easter Eggs</h3>
            <p class="mt-2 text-gray-700">
                Our terminal is full of hidden easter eggs. Here are some you might discover:
            </p>

            <ul class="list-disc pl-5 mt-2 text-gray-700">
                <li>Type <code class="terminal-highlight">konami</code> to activate the Konami code (if you can remember
                    it)
                </li>
                <li>Type <code class="terminal-highlight">matrix</code> to see the Matrix (or at least a poor imitation)
                </li>
                <li>Type <code class="terminal-highlight">hack</code> to pretend you're a hacker</li>
                <li>Type <code class="terminal-highlight">life</code> to get the meaning of life (spoiler: it's 42)</li>
                <li>Type <code class="terminal-highlight">coffee --type all</code> to see all coffee types (there are
                    many)</li>
            </ul>

            <h2 class="text-xl font-semibold text-gray-900 mt-8">Troubleshooting</h2>
            <p class="mt-2 text-gray-700">
                If you encounter any issues with the Calkeo Terminal, here are some troubleshooting steps:
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

            <h2 class="text-xl font-semibold text-gray-900 mt-8">ASCII Art</h2>
            <p class="mt-2 text-gray-700">
                Our terminal supports ASCII art. Here's an example:
            </p>

            <div class="terminal-ascii my-4">
                _____ _ _
                / ____| | | | |
                | | ___ | | ___ _ __ | |_ ___
                | | / _ \| |/ _ \| '_ \| __/ _ \
                | |___| (_) | | (_) | | | | || __/
                \_____\___/|_|\___/|_| |_|\__\___|

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