<?php

namespace App\Commands;

use App\Livewire\Terminal;
use Illuminate\Support\Carbon;
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

        try {
            $whois = WhoisFactory::get()->createWhois();
            $info = $whois->loadDomainInfo($domain);

            if (!$info) {
                $output[] = $this->streamOutput($terminal, $this->formatOutput("Domain is available!", 'success'));
                return $output;
            }

            // Create a styled box with domain information
            $domainInfo = [
                $this->formatOutput("Domain Information", 'header'),
                $this->formatOutput("==================", 'subheader'),
                $this->formatOutput("Domain: " . $domain, 'value'),
                $this->formatOutput("Status: " . ($info->states ? implode(', ', $info->states) : 'Active'), 'info'),
                $this->formatOutput("Expires: " . Carbon::createFromTimestamp($info->expirationDate)->format('jS F Y'), 'warning'),
                $this->formatOutput("Created: " . Carbon::createFromTimestamp($info->creationDate)->format('jS F Y'), 'info'),
                $this->formatOutput("Updated: " . ($info->updatedDate ? Carbon::createFromTimestamp($info->updatedDate)->format('jS F Y') : 'Unknown'), 'info'),
                $this->formatOutput("Registrar: " . $info->registrar, 'value'),
                $this->formatOutput("Name Servers: " . ($info->nameServers ? implode(', ', $info->nameServers) : 'Unknown'), 'info'),
            ];

            $output[] = $this->streamOutput($terminal, $this->createStyledBox($domainInfo));

            // Add owner information if available
            if ($info->owner) {
                $ownerInfo = [
                    $this->formatOutput("Owner Information", 'header'),
                    $this->formatOutput("==================", 'subheader'),
                    $this->formatOutput("Owner: " . $info->owner, 'value'),
                ];

                if ($info->registrant) {
                    $ownerInfo[] = $this->formatOutput("Registrant: " . $info->registrant, 'info');
                }

                if ($info->administrativeContact) {
                    $ownerInfo[] = $this->formatOutput("Admin Contact: " . $info->administrativeContact, 'info');
                }

                if ($info->technicalContact) {
                    $ownerInfo[] = $this->formatOutput("Technical Contact: " . $info->technicalContact, 'info');
                }

                $output[] = $this->streamOutput($terminal, $this->createStyledBox($ownerInfo));
            }

            // Add a note about data freshness
            $output[] = $this->streamOutput($terminal, $this->formatOutput("Note: Whois data may be delayed or inaccurate. For the most accurate information, contact the registrar directly.", 'warning'));
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
