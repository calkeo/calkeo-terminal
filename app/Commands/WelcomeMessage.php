<?php

namespace App\Commands;

use Illuminate\Support\Str;

class WelcomeMessage
{
    /**
     * Format the welcome message with terminal styling
     *
     * @return string
     */
    public function format(): string
    {
        $date = date('D M j H:i:s T Y');

        $html = <<<HTML
        <div class="border border-gray-700 rounded-sm my-2">
            <!-- Header -->
            <div class="bg-gray-800 px-2 py-1 border-b border-gray-700">
                <span class="text-cyan-400 font-bold">{$this->getAppName()} Terminal {$this->getAppVersion()}</span>
            </div>

            <!-- Content -->
            <div class="p-2">
                <!-- Features -->
                <div class="mb-2">
                    <span class="text-yellow-400">*</span> Now with 100% more terminal!<br>
                    <span class="text-yellow-400">*</span> Featuring the revolutionary "help" command<br>
                    <span class="text-yellow-400">*</span> Includes state-of-the-art "clear" technology<br>
                    <span class="text-yellow-400">*</span> Powered by pure caffeine and determination
                </div>

                <!-- System info -->
                <div class="mb-2">
                    System information as of <span class="text-cyan-400">{$date}</span><br>
                    Kernel: <span class="text-blue-400">3.14.159</span> (GNU/Linux x86_64)<br>
                    CPU: <span class="text-blue-400">Intel(R) Celeron(TM) 300A @ 300MHz</span><br>
                    Memory: <span class="text-blue-400">1TB Optane Memory</span><br>
                    Disk: <span class="text-blue-400">64GB CompactFlash</span>
                </div>

                <!-- Help text -->
                <div class="mb-2">
                    Type <span class="text-purple-400">help</span> to see available commands
                </div>
            </div>
        </div>

        <!-- Welcome message -->
        <div class="my-2">
            Welcome to <span class="text-cyan-400">{$this->getAppName()} {$this->getAppVersion()}</span> (GNU/Linux 3.14.159 x86_64)<br><br>
            <span class="text-yellow-400">*</span> Documentation: <a target="_blank" href="/docs" class="text-blue-400 hover:underline">https://calkeo.dev/docs</a><br>
            <span class="text-yellow-400">*</span> Management: <a target="_blank" href="/manage" class="text-blue-400 hover:underline">https://calkeo.dev/manage</a><br>
            <span class="text-yellow-400">*</span> Support: <a target="_blank" href="/support" class="text-blue-400 hover:underline">https://calkeo.dev/support</a> <span class="text-pink-400">&nbsp;(Premium support available!)</span>
        </div>
HTML;

        return $this->cleanHtml($html);
    }

    /**
     * Clean HTML by removing unwanted whitespace and newlines
     *
     * @param  string   $html
     * @return string
     */
    private function cleanHtml(string $html): string
    {
        // Remove comments
        $html = preg_replace('/<!--.*?-->/s', '', $html);

        // Use Laravel's Str facade to clean the HTML
        return Str::of($html)
            ->replaceMatches('/>\s+</', '><')
            ->replaceMatches('/\s+/', ' ')
            ->trim();
    }

    /**
     * Get the application name
     *
     * @return string
     */
    private function getAppName(): string
    {
        return config('app.name');
    }

    /**
     * Get the application version
     *
     * @return string
     */
    private function getAppVersion(): string
    {
        return config('app.version');
    }
}
