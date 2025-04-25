<div x-data="{ isMobile: window.innerWidth < 768 }"
    x-init="window.addEventListener('resize', () => isMobile = window.innerWidth < 768)"
    class="h-full w-full p-0 m-0 flex items-center justify-center">
    {{-- Mobile View --}}
    <div x-cloak x-show="isMobile" class="h-full flex flex-col items-center justify-center p-6 text-center">
        <div class="max-w-md">
            <div class="mb-6">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-cyan-400" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-cyan-400 mb-4">Desktop Experience Recommended</h2>
            <p class="text-green-400 mb-6">The terminal interface is optimized for desktop browsers. For the best
                experience, please visit on a larger screen.</p>
            <div class="flex flex-col space-y-4">
                <button wire:click="viewPlainText"
                    class="bg-gray-800 hover:bg-gray-700 text-green-400 px-4 py-2 rounded transition-colors">
                    Visit Basic Website
                </button>
                <button @click="isMobile = false" class="text-cyan-400 hover:text-cyan-300 transition-colors">
                    Continue Anyway
                </button>
            </div>
        </div>
    </div>

    <div x-cloak x-show="!isMobile" class="w-full max-w-lg">
        <div class="bg-gray-950 rounded-lg overflow-hidden shadow-2xl">
            {{-- Terminal Header --}}
            <div class="flex items-center p-2 bg-gray-950 border-b border-gray-800">
                <div class="text-sm text-green-400">login</div>
            </div>

            {{-- Login Content --}}
            <div class="p-6 bg-black">
                <div class="space-y-4 text-green-400">
                    <div class="text-cyan-400">Welcome to {{ config('app.name') }} {{ config('app.version') }}</div>
                    <div class="text-sm text-gray-200">{{ $tagline }}</div>

                    {{-- Subtle hint --}}
                    <div class="text-xs text-gray-600 italic">Enter any credentials to continue...</div>

                    <div class="flex items-center">
                        <span class="mr-2">Username:</span>
                        <input type="text" wire:model="username" wire:keydown.enter="focusPassword"
                            class="flex-1 bg-transparent border-none outline-none text-green-400 focus:ring-0"
                            placeholder="Username" autofocus>
                    </div>
                    <div class="flex items-center">
                        <span class="mr-2">Password:</span>
                        <input type="password" wire:model="password" wire:keydown.enter="login"
                            class="flex-1 bg-transparent border-none outline-none text-green-400 focus:ring-0"
                            placeholder="Password">
                    </div>
                    @if($error)
                    <div class="text-red-500">{{ $error }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>