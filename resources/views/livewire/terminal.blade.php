<div class="h-full w-full p-0 m-0">
    <div class="h-full flex flex-col">
        <!-- Terminal Header -->
        <div class="flex items-center p-2 bg-gray-950 border-b border-gray-800">
            <div class="text-xs text-cyan-400">{{ $username }}@calkeo.dev ~ </div>
        </div>

        <!-- Terminal Content -->
        <div class="flex-1 bg-black p-4 overflow-y-auto text-green-400 space-y-2 scroll-smooth" id="terminal-container">
            <!-- Output Area -->
            <div id="terminal-output" wire:stream="output">
                @foreach($output as $line)
                <div class="whitespace-pre-wrap leading-relaxed">{!! $line !!}</div>
                @endforeach
            </div>

            <!-- Command Input -->
            @unless($hideInput)
            <div class="flex items-center mt-3">
                @if(!$currentCommandName)
                <span class="text-cyan-400 mr-2">{{ $username }}@calkeo.dev:</span>
                <span class="text-yellow-500 mr-1">~</span>
                <span class="text-green-400 mr-2">$</span>
                @else
                <span class="text-purple-400 mr-1">&gt;</span>
                @endif

                <input type="text" id="terminal-input" wire:model="command" wire:keydown.enter="executeCommand"
                    wire:keydown.up.prevent="getPreviousCommand" wire:keydown.down.prevent="getNextCommand"
                    wire:keydown.ctrl.c.prevent="clearCommand" wire:keydown.tab.prevent="handleTabCompletion"
                    class="flex-1 bg-transparent border-none outline-none @if(!$currentCommandName) text-green-400 @else text-purple-400 @endif focus:ring-0"
                    @if(!$currentCommandName) placeholder="Type a command..." @endif autofocus>
            </div>
            @endunless

            <!-- Suggestions -->
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