<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title') - calkeOS</title>
        @vite(['resources/css/dashboard.css', 'resources/js/app.js'])
        <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet">
        <style>
            [x-cloak] {
                display: none !important;
            }
        </style>
    </head>

    <body class="bg-gray-50 text-gray-800 font-sans">
        <header class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex">
                        <div class="flex-shrink-0 flex items-center">
                            <span class="text-2xl font-bold text-indigo-600">calkeOS</span>
                        </div>
                        <nav class="ml-6 flex space-x-8">
                            <a wire:navigate href="/docs"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('docs') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Documentation
                            </a>
                            <a href="/manage"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('manage') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Management
                            </a>
                            <a href="/support"
                                class="inline-flex items-center px-1 pt-1 border-b-2 {{ request()->is('support') ? 'border-indigo-500 text-gray-900' : 'border-transparent text-gray-500 hover:border-gray-300 hover:text-gray-700' }} text-sm font-medium">
                                Support
                            </a>
                        </nav>
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">
                @yield('content')
            </div>
        </main>

        <footer class="bg-white border-t border-gray-200 mt-12">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center">
                    <div class="text-sm text-gray-500">
                        © {{ date('Y') }} calkeOS. All rights reserved.
                    </div>
                    <div class="text-sm text-gray-500">
                        @yield('footer_text')
                    </div>
                </div>
            </div>
        </footer>

        @yield('scripts')
    </body>

</html>