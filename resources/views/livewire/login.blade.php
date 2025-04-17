<div class="h-full w-full p-0 m-0 flex items-center justify-center">
    <div class="w-full max-w-lg">
        <div class="bg-gray-950 rounded-lg overflow-hidden shadow-2xl">
            <!-- Terminal Header -->
            <div class="flex items-center p-2 bg-gray-950 border-b border-gray-800">
                <div class="text-sm text-green-400 font-['JetBrains_Mono']">login</div>
            </div>

            <!-- Login Content -->
            <div class="p-6 bg-black">
                <div class="space-y-4 font-['JetBrains_Mono'] text-green-400">
                    <div class="text-cyan-400">Welcome to calkeOS v1.0.0</div>
                    <div class="text-sm text-gray-500">{{ $tagline }}</div>
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
