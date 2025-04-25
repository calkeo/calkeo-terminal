<?php

namespace App\Commands;

use App\Livewire\Terminal;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory as WhoisFactory;

class WhoisCommand extends AbstractCommand
{
    protected $name = 'whois';
    protected $description = 'Perform a whois lookup on a domain';

    public function execute(Terminal $terminal, array $args = []): array
    {
        $output = [];
        $domain = $args[0] ?? null;

        if (!$domain) {
            $output[] = $this->formatOutput('Usage: whois &lt;domain&gt;', 'error');
            return $output;
        }

        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN, FILTER_FLAG_HOSTNAME)) {
            $output[] = $this->formatOutput('Invalid domain name format', 'error');
            return $output;
        }

        $terminal->formattedCommand = '';

        $terminal->dispatch('stream-start');

        $output[] = $this->streamOutput($terminal, $this->formatOutput('Performing whois lookup on ' . $domain, 'subheader'));

        $output[] = $this->streamOutput($terminal, $this->formatOutput('=========================', 'subheader'));

        try {
            $whois = WhoisFactory::get()->createWhois();
            $info = $whois->loadDomainInfo($domain);

            if (!$info) {
                $output[] = $this->streamOutput($terminal, $this->formatOutput("Domain is available!", 'success'));
                return $output;
            }

            $output[] = $this->streamOutput($terminal, $this->formatOutput("Registered to: " . $info->owner, 'white'));
            $output[] = $this->streamOutput($terminal, $this->formatOutput("Expires at: " . date("d.m.Y H:i:s", $info->expirationDate), 'white'));
            $output[] = $this->streamOutput($terminal, $this->formatOutput("Registered on: " . date("d.m.Y H:i:s", $info->creationDate), 'white'));
            $output[] = $this->streamOutput($terminal, $this->formatOutput("Registered at: " . $info->registrar, 'white'));
        } catch (ConnectionException $e) {
            $output[] = $this->streamOutput($terminal, $this->formatOutput("Disconnect or connection timeout", 'error'));
        } catch (ServerMismatchException $e) {
            $output[] = $this->streamOutput($terminal, $this->formatOutput("TLD server not found in current server hosts", 'error'));
        } catch (WhoisException $e) {
            $output[] = $this->streamOutput($terminal, $this->formatOutput("Whois server responded with error '{$e->getMessage()}'", 'error'));
        }

        $terminal->dispatch('stream-complete');

        return $output;
    }
}
