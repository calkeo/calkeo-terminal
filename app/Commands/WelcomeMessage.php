<?php

namespace App\Commands;

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

        $html = '<div class="font-mono text-sm border border-gray-700 rounded-sm my-2">';

        // Header
        $html .= '<div class="bg-gray-800 px-2 py-1 border-b border-gray-700">';
        $html .= '<span class="text-cyan-400 font-bold">calkeOS Terminal v1.0.0</span>';
        $html .= '</div>';

        // Content
        $html .= '<div class="p-2">';

        // Features
        $html .= '<div class="mb-2">';
        $html .= '<span class="text-yellow-400">*</span> Now with 100% more terminal!<br>';
        $html .= '<span class="text-yellow-400">*</span> Featuring the revolutionary "help" command<br>';
        $html .= '<span class="text-yellow-400">*</span> Includes state-of-the-art "clear" technology<br>';
        $html .= '<span class="text-yellow-400">*</span> Powered by pure caffeine and determination';
        $html .= '</div>';

        // System info
        $html .= '<div class="mb-2">';
        $html .= 'System information as of <span class="text-cyan-400">' . $date . '</span><br>';
        $html .= 'Kernel: <span class="text-blue-400">6.9.420</span> (GNU/Linux x86_64)<br>';
        $html .= 'CPU: <span class="text-blue-400">Intel(R) Caffeine(TM) i9 9999K @ 4.20GHz</span><br>';
        $html .= 'Memory: <span class="text-blue-400">42GB of pure determination</span><br>';
        $html .= 'Disk: <span class="text-blue-400">1TB of possibilities</span>';
        $html .= '</div>';

        // Help text
        $html .= '<div class="mb-2">';
        $html .= 'Type <span class="text-purple-400">help</span> to see available commands';
        $html .= '</div>';

        $html .= '</div>'; // End content
        $html .= '</div>'; // End box

        // Welcome message
        $html .= '<div class="font-mono text-sm my-2">';
        $html .= 'Welcome to <span class="text-cyan-400">calkeOS v1.0.0</span> (GNU/Linux 6.9.420 x86_64)<br><br>';
        $html .= '<span class="text-yellow-400">*</span> Documentation: <span class="text-blue-400">https://docs.calkeos.dev</span><br>';
        $html .= '<span class="text-yellow-400">*</span> Management: <span class="text-blue-400">https://manage.calkeos.dev</span><br>';
        $html .= '<span class="text-yellow-400">*</span> Support: <span class="text-blue-400">https://support.calkeos.dev</span> <span class="text-pink-400">(Premium support available!)</span>';
        $html .= '</div>';

        return $html;
    }
}
