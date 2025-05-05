<?php

namespace App\Commands;

use App\Commands\Traits\InteractiveCommandTrait;
use App\Livewire\Terminal;

class ForbiddenCommand extends AbstractCommand
{
    use InteractiveCommandTrait;

    protected $name = 'forbidden';
    protected $description = 'Detect and block potentially dangerous commands';
    protected $hidden = true;
    protected $aliases = [];

    // Session keys
    protected const COMMAND_KEY = 'command';

    // Step definitions
    protected const STEP_CONFIRM = 1;

    // Warning messages
    protected const WARNING_MESSAGES = [
        "⚠️ This command is restricted to admins only. Are you sure you want to proceed?",
        "⚠️ Warning: This command could cause irreversible damage. Proceed?",
        "⚠️ Danger! This command requires elevated privileges. Continue?",
        "⚠️ Access denied! This command is for authorized personnel only. Override?",
        "⚠️ Security alert! This command is locked. Attempt to unlock?",
        "⚠️ System integrity at risk! This command is protected. Bypass protection?",
        "⚠️ Critical command detected! This requires special authorization. Authorize?",
        "⚠️ Restricted command! This could affect system stability. Override restrictions?",
        "⚠️ Protected command! This requires root access. Attempt to gain access?",
        "⚠️ Warning: This command is in the restricted zone. Enter anyway?",
    ];

    // Success messages
    protected const SUCCESS_MESSAGES = [
        "Wise decision! It's better to be safe than sorry.",
        "Good call! Some commands are best left to the experts.",
        "Smart thinking! System security is everyone's responsibility.",
        "Prudent choice! Better to ask for help than risk system stability.",
        "Good judgment! Some commands require special training.",
        "Responsible choice! System integrity is important.",
        "Smart move! Some commands are restricted for good reason.",
        "Good thinking! Better to be cautious with system commands.",
        "Wise choice! Some commands are best left to administrators.",
        "Good decision! System security starts with responsible users.",
    ];

    // Proceed messages (shown before redirect)
    protected const PROCEED_MESSAGES = [
        "You've been warned! Proceed with caution...",
        "Brace yourself! You're about to enter the danger zone...",
        "System override initiated! Prepare for the unexpected...",
        "Access granted! But at what cost...",
        "Security bypassed! You're on your own now...",
        "Warning ignored! The system is at your mercy...",
        "Protection disabled! You're playing with fire...",
        "Restrictions lifted! But remember, you asked for this...",
        "Command authorized! But don't say we didn't warn you...",
        "System integrity compromised! You're in uncharted territory...",
    ];

    // Troll URLs for redirects
    protected const TROLL_URLS = [
        // Classic video/audio memes
        'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Rickroll
        'https://www.youtube.com/watch?v=2Z4m4lnjxkY', // Trololo
        'https://www.youtube.com/watch?v=QH2-TGUlwu4', // Nyan Cat
        'https://www.youtube.com/watch?v=ZZ5LpwO-An4', // He-Man sings
        'https://www.youtube.com/watch?v=j5a0jTc9S10', // Darude - Sandstorm

        // Audio jump scares or sound-based trolls
        'http://endless.horse/', // The Endless Horse
        'https://longdogechallenge.com/', // Very long doge

        // Weird generators or tools
        'https://pointerpointer.com/', // Finds a photo pointing to your cursor
        'http://corndog.io/', // Just a corndog image
        'https://zoomquilt.org/', // Never-ending zoom art
        'https://zombo.com/', // "You can do anything at Zombo.com"

        // Meme sites
        'https://www.omfgdogs.com/', // Animated dogs with music

        // NEW - Punishment-grade trolls
        'https://crouton.net/', // Infinite Rickroll popups
        'https://jacksonpollock.org/', // Useless mouse painting
        'https://heeeeeeeey.com/', // Endless "hey"
        'https://hooooooooo.com/', // Endless "ho"
        'https://thatsthefinger.com/', // Middle finger
        'https://sometimesredsometimesblue.com/', // Color flickers randomly
        'https://pixelsfighting.com/', // Two pixels fighting forever
        'https://fakeupdate.net/windows98k/', // Fake Windows 98K update
        'https://fakeupdate.net/wnc/',
        'https://fallingfalling.com/', // Disorienting audio/visual overload
        'https://thisman.org/', // "Have you dreamed this man?" conspiracy
        'https://sliding.toys/mystic-square/8-puzzle/daily/',
        'https://longdogechallenge.com/',
        'https://maze.toys/mazes/mini/daily/',
        'https://optical.toys',
        'https://paint.toys/calligram/',
        'https://puginarug.com',
        'https://memory.toys/classic/easy/',
        'https://alwaysjudgeabookbyitscover.com',
        'https://clicking.toys/flip-grid/neat-nine/3-holes/',
        'https://weirdorconfusing.com/',
        'https://checkbox.toys/scale/',
        'https://binarypiano.com/',
        'https://mondrianandme.com/',
        'https://onesquareminesweeper.com/',
        'https://cursoreffects.com',
        'http://floatingqrcode.com/',
        'https://thatsthefinger.com/',
        'https://cant-not-tweet-this.com/',
        'http://heeeeeeeey.com/',
        'http://corndog.io/',
        'http://eelslap.com/',
        'http://www.staggeringbeauty.com/',
        'http://burymewithmymoney.com/',
        'https://smashthewalls.com/',
        'https://jacksonpollock.org/',
        'http://endless.horse/',
        'http://drawing.garden/',
        'https://www.trypap.com/',
        'http://www.republiquedesmangues.fr/',
        'http://www.movenowthinklater.com/',
        'https://sliding.toys/mystic-square/15-puzzle/daily/',
        'https://paint.toys/',
        'https://checkboxrace.com/',
        'http://www.rrrgggbbb.com/',
        'http://www.koalastothemax.com/',
        'https://rotatingsandwiches.com/',
        'http://www.everydayim.com/',
        'http://randomcolour.com/',
        'http://maninthedark.com/',
        'http://cat-bounce.com/',
        'http://chrismckenzie.com/',
        'https://thezen.zone/',
        'http://ninjaflex.com/',
        'http://ihasabucket.com/',
        'https://toms.toys',
        'http://corndogoncorndog.com/',
        'http://www.hackertyper.com/',
        'https://pointerpointer.com',
        'http://imaninja.com/',
        'http://www.partridgegetslucky.com/',
        'http://www.ismycomputeron.com/',
        'http://www.nullingthevoid.com/',
        'http://www.muchbetterthanthis.com/',
        'http://www.yesnoif.com/',
        'http://lacquerlacquer.com',
        'https://clicking.toys/peg-solitaire/solid/',
        'http://potatoortomato.com/',
        'http://iamawesome.com/',
        'https://strobe.cool/',
        'http://thisisnotajumpscare.com/',
        'http://doughnutkitten.com/',
        'http://crouton.net/',
        'http://corgiorgy.com/',
        'http://www.wutdafuk.com/',
        'http://unicodesnowmanforyou.com/',
        'http://chillestmonkey.com/',
        'http://scroll-o-meter.club/',
        'http://www.crossdivisions.com/',
        'https://boringboringboring.com/',
        'http://www.patience-is-a-virtue.org/',
        'http://pixelsfighting.com/',
        'http://isitwhite.com/',
        'https://existentialcrisis.com/',
        'http://onemillionlols.com/',
        'http://www.omfgdogs.com/',
        'http://oct82.com/',
        'http://chihuahuaspin.com/',
        'http://www.blankwindows.com/',
        'http://tunnelsnakes.com/',
        'http://www.trashloop.com/',
        'http://spaceis.cool/',
        'http://www.doublepressure.com/',
        'http://www.donothingfor2minutes.com/',
        'http://buildshruggie.com/',
        'https://optical.toys/thatcher-effect/',
        // 'http://buzzybuzz.biz/', cert
        'http://yeahlemons.com/',
        'http://wowenwilsonquiz.com',
        // 'https://thepigeon.org/', expired
        'http://notdayoftheweek.com/',
        'https://number.toys/',
        'https://card.toys',
        'http://www.amialright.com/',
        'https://greatbignothing.com/',
        'https://zoomquilt.org/',
        "https://optical.toys/troxler-fade/",
        'https://dadlaughbutton.com/',
        'https://remoji.com/',
        'http://papertoilet.com/',
        'https://loopedforinfinity.com/',
        "https://www.ripefordebate.com/",
        'https://end.city/',
        'https://www.bouncingdvdlogo.com/',
        'https://toybox.toms.toys',
    ];

    protected $terminal;

    public function __construct()
    {
        $this->aliases = array_merge(
            array_keys(self::DANGEROUS_COMMANDS),
            ...array_values(self::DANGEROUS_COMMANDS)
        );
    }

    /**
     * Execute the command
     *
     * @param  Terminal $terminal
     * @param  array    $args
     * @return array
     */
    public function execute(Terminal $terminal, array $args = []): array
    {
        $this->terminal = $terminal;

        // Get current step from session
        $step = $this->getCurrentStep();

        // If we have arguments or are in the middle of a process, handle the step
        if (!empty($args) || $step > 1) {
            return $this->handleStep($args, $step);
        }

        // Start the interactive process
        return $this->startInteractiveProcess();
    }

    protected function getTerminal(): Terminal
    {
        return $this->terminal;
    }

    protected function getSessionKeys(): array
    {
        return [
            self::COMMAND_KEY,
        ];
    }

    protected function startInteractiveProcess(): array
    {
        // Reset session data
        $this->clearSession();
        $this->setCurrentStep(self::STEP_CONFIRM);

        $warningMessage = self::WARNING_MESSAGES[array_rand(self::WARNING_MESSAGES)];

        return $this->interactiveOutput([
            $this->formatOutput($warningMessage, 'error'),
            $this->formatOutput("Type 'yes' to confirm or 'no' to cancel:", 'warning'),
        ]);
    }

    protected function handleStep(array $args, int $step): array
    {
        $input = strtolower(trim($args[0] ?? ''));

        if ($input === 'y' || $input === 'yes') {
            $this->clearSession();
            $trollUrl = self::TROLL_URLS[array_rand(self::TROLL_URLS)];
            $proceedMessage = self::PROCEED_MESSAGES[array_rand(self::PROCEED_MESSAGES)];

            $this->terminal->js('setTimeout(() => window.open("' . $trollUrl . '", "_blank"), 2000);');

            return [
                $this->formatOutput($proceedMessage, 'error'),
            ];
        } elseif ($input === 'n' || $input === 'no') {
            $this->clearSession();
            $successMessage = self::SUCCESS_MESSAGES[array_rand(self::SUCCESS_MESSAGES)];
            return [
                $this->formatOutput($successMessage, 'success'),
            ];
        }

        return $this->interactiveOutput([
            $this->formatOutput("Please type 'yes' or 'no':", 'warning'),
        ]);
    }

    // List of dangerous command patterns and their aliases
    protected const DANGEROUS_COMMANDS = [
        'rm' => ['remove', 'delete', 'del', 'erase'],
        'rmdir' => ['rd', 'remove-directory'],
        'format' => ['wipe', 'erase-disk'],
        'dd' => ['disk-destroyer'],
        'chmod' => ['chmod', 'chown', 'chgrp'],
        'sudo' => ['su', 'admin', 'root'],
        'kill' => ['terminate', 'end', 'stop'],
        'shutdown' => ['poweroff', 'halt', 'reboot'],
        'mv' => ['move', 'rename'],
        'cp' => ['copy'],
        'cat' => ['type', 'more', 'less'],
        'grep' => ['find', 'search'],
        'sed' => ['stream-editor'],
        'awk' => ['pattern-scanning'],
        'tar' => ['archive', 'compress'],
        'zip' => ['compress', 'archive'],
        'unzip' => ['extract', 'decompress'],
        'curl' => ['wget', 'fetch'],
        'scp' => ['secure-copy'],
        'ftp' => ['file-transfer'],
        'telnet' => ['remote-login'],
        'nc' => ['netcat', 'network-utility'],
        'nmap' => ['network-mapper'],
        'tcpdump' => ['packet-analyzer'],
        'iptables' => ['firewall'],
        'chroot' => ['change-root'],
        'mount' => ['attach'],
        'umount' => ['detach'],
        'fdisk' => ['partition'],
        'mkfs' => ['make-filesystem'],
        'fsck' => ['filesystem-check'],
        'passwd' => ['change-password'],
        'useradd' => ['add-user'],
        'userdel' => ['delete-user'],
        'groupadd' => ['add-group'],
        'groupdel' => ['delete-group'],
        'crontab' => ['schedule-tasks'],
        'at' => ['schedule-once'],
    ];
}
