<div class="h-full w-full p-0 m-0" x-data="{ isMobile: window.innerWidth < 768 }"
    x-init="window.addEventListener('resize', () => isMobile = window.innerWidth < 768)">
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

    {{-- Desktop Terminal View --}}
    <div x-cloak x-show="!isMobile" class="h-full flex flex-col">
        {{-- Terminal Header --}}
        <div class="flex items-center p-2 bg-gray-950 border-b border-gray-800">
            <div class="text-xs text-cyan-400">{{ $username }}@calkeo.dev ~ </div>
        </div>

        {{-- Terminal Content --}}
        <div class="flex-1 bg-black p-4 overflow-y-auto text-green-400 space-y-2 scroll-smooth min-h-0"
            id="terminal-container">
            {{-- Output Area --}}
            <div id="terminal-output" wire:stream="output">
                @foreach($output as $line)
                <div class="whitespace-pre-wrap leading-relaxed">{!! $line !!}</div>
                @endforeach
            </div>

            {{-- Command Input --}}
            @unless($hideInput)
            <div class="flex items-center mt-3">
                @if(!$currentCommandName)
                <span class="text-cyan-400 mr-2">{{ $username }}@calkeo.dev:</span>
                <span class="text-yellow-500 mr-1">~</span>
                <span class="text-green-400 mr-2">$</span>
                @else
                <span class="text-purple-400 mr-1">&gt;</span>
                @endif

                <input type="text" id="terminal-input"
                    x-data="{ clearOnSubmit() { $wire.executeCommand(); this.$el.value = ''; } }" wire:model="command"
                    @keydown.enter.prevent="clearOnSubmit" wire:keydown.up.prevent="getPreviousCommand"
                    wire:keydown.down.prevent="getNextCommand" wire:keydown.ctrl.c.prevent="clearCommand"
                    wire:keydown.tab.prevent="handleTabCompletion"
                    class="flex-1 bg-transparent border-none outline-none @if(!$currentCommandName) text-green-400 @else text-purple-400 @endif focus:ring-0"
                    @if(!$currentCommandName) placeholder="Type a command..." @endif autofocus>
            </div>
            @endunless

            {{-- Suggestions --}}
            @if($showSuggestions && count($suggestions) > 0)
            <div class="mt-3 text-green-400">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    @foreach($suggestions as $suggestion)
                    <div wire:click="selectSuggestion('{{ $suggestion }}')"
                        class="cursor-pointer hover:bg-gray-800 px-2 py-1 rounded">
                        {{ $suggestion }}
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>