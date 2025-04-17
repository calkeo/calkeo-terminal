<div class="h-full w-full p-0 m-0">
    <div class="h-full flex flex-col">
        <!-- Terminal Header -->
        <div class="flex items-center p-2 bg-gray-950 border-b border-gray-800">
            <div class="text-xs text-cyan-400 font-['JetBrains_Mono']">{{ $username }}@calkeo.dev ~ </div>
        </div>

        <!-- Terminal Content -->
        <div class="flex-1 bg-black p-4 overflow-hidden flex flex-col">
            <!-- Output Area -->
            <div id="terminal-output"
                class="flex-1 overflow-y-auto font-['JetBrains_Mono'] text-sm text-green-400 space-y-1 scroll-smooth">
                @foreach($output as $line)
                <div class="whitespace-pre-wrap leading-relaxed">{!! $line !!}</div>
                @endforeach
            </div>

            <!-- Command Input -->
            <div class="flex items-center mt-3 font-['JetBrains_Mono']">
                <span class="text-cyan-400 mr-2 text-sm">{{ $username }}@calkeo.dev:</span>
                <span class="text-yellow-500 mr-1 text-sm">~</span>
                <span class="text-green-400 mr-2 text-sm">$</span>
                <input type="text" wire:model="command" wire:keydown.enter="executeCommand"
                    wire:keydown.up.prevent="getPreviousCommand" wire:keydown.down.prevent="getNextCommand"
                    wire:keydown.ctrl.c.prevent="clearCommand" wire:keydown.tab.prevent="handleTabCompletion"
                    class="flex-1 bg-transparent border-none outline-none text-green-400 focus:ring-0 text-sm"
                    placeholder="Type a command..." autofocus>
            </div>

            <!-- Suggestions -->
            @if($showSuggestions && count($suggestions) > 0)
            <div class="mt-3 text-green-400">
                <div class="grid grid-cols-2 md:grid-cols-3 gap-2 text-sm">
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

<script>
    // Function to scroll to the bottom of the terminal output
    function scrollToBottom() {
        const outputArea = document.getElementById('terminal-output');
        if (outputArea) {
            outputArea.scrollTop = outputArea.scrollHeight;
        }
    }

    // Scroll to bottom when the component updates
    document.addEventListener('livewire:update', scrollToBottom);

    // Also scroll after any DOM changes
    document.addEventListener('DOMContentLoaded', function() {
        // Initial scroll
        scrollToBottom();

        // Set up a MutationObserver to watch for changes in the terminal output
        const outputArea = document.getElementById('terminal-output');
        if (outputArea) {
            const observer = new MutationObserver(scrollToBottom);
            observer.observe(outputArea, { childList: true, subtree: true });
        }

        // Also scroll after a short delay to ensure content is rendered
        setTimeout(scrollToBottom, 100);
    });

    // Scroll after any Livewire event
    document.addEventListener('livewire:load', scrollToBottom);
    document.addEventListener('livewire:navigated', scrollToBottom);
    document.addEventListener('livewire:initialized', scrollToBottom);
</script>