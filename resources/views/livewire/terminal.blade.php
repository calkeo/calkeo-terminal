<div class="h-full w-full p-0 m-0">
    <div class="h-full flex flex-col">
        <!-- Terminal Header -->
        <div class="flex items-center p-2 bg-gray-950 border-b border-gray-800">
            <div class="text-xs text-cyan-400 font-['JetBrains_Mono']">{{ $username }}@calkeo.dev ~ </div>
        </div>

        <!-- Terminal Content -->
        <div class="flex-1 bg-black p-4 overflow-y-auto font-['JetBrains_Mono'] text-sm text-green-400 space-y-2 scroll-smooth"
            id="terminal-container">
            <!-- Output Area -->
            <div id="terminal-output" wire:stream="output">
                @foreach($output as $line)
                <div class="whitespace-pre-wrap leading-relaxed">{!! $line !!}</div>
                @endforeach
            </div>

            <!-- Command Input -->
            <div class="flex items-center mt-3">
                @if(!$currentCommandName)
                <span class="text-cyan-400 mr-2 text-sm">{{ $username }}@calkeo.dev:</span>
                <span class="text-yellow-500 mr-1 text-sm">~</span>
                <span class="text-green-400 mr-2 text-sm">$</span>
                @else
                <span class="text-purple-400 mr-1 text-sm">&gt;</span>
                @endif
                <input type="text" wire:model="command" wire:keydown.enter="executeCommand"
                    wire:keydown.up.prevent="getPreviousCommand" wire:keydown.down.prevent="getNextCommand"
                    wire:keydown.ctrl.c.prevent="clearCommand" wire:keydown.tab.prevent="handleTabCompletion"
                    class="flex-1 bg-transparent border-none outline-none @if(!$currentCommandName) text-green-400 @else text-purple-400 @endif focus:ring-0 text-sm"
                    @if(!$currentCommandName) placeholder="Type a command..." @endif autofocus>
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
        const terminalContainer = document.getElementById('terminal-container');
        if (terminalContainer) {
            terminalContainer.scrollTop = terminalContainer.scrollHeight;
        }
    }

    // Scroll to bottom when the component updates
    document.addEventListener('livewire:update', scrollToBottom);

    // Also scroll after any DOM changes
    document.addEventListener('DOMContentLoaded', function() {
        // Initial scroll
        scrollToBottom();

        // Set up a MutationObserver to watch for changes in the terminal output
        const terminalContent = document.getElementById('terminal-output');
        if (terminalContent) {
            const observer = new MutationObserver(scrollToBottom);
            observer.observe(terminalContent, { childList: true, subtree: true });
        }

        // Also scroll after a short delay to ensure content is rendered
        setTimeout(scrollToBottom, 100);
    });

    // Scroll after any Livewire event
    document.addEventListener('livewire:load', scrollToBottom);
    document.addEventListener('livewire:navigated', scrollToBottom);
    document.addEventListener('livewire:initialized', scrollToBottom);
</script>