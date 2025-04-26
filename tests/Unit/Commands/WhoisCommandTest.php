<?php

namespace Tests\Unit\Commands;

use App\Commands\WhoisCommand;
use App\Livewire\Terminal;
use Iodev\Whois\DomainInfo;
use Iodev\Whois\Exceptions\ConnectionException;
use Iodev\Whois\Exceptions\ServerMismatchException;
use Iodev\Whois\Exceptions\WhoisException;
use Iodev\Whois\Factory as WhoisFactory;
use Iodev\Whois\Whois;
use Mockery;
use Tests\TestCase;
use Tests\Traits\TerminalTestTrait;

class WhoisCommandTest extends TestCase
{
    use TerminalTestTrait;

    protected $command;
    protected $terminal;
    protected $whois;
    protected $whoisServer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->terminal = Mockery::mock(Terminal::class);
        $this->terminal->shouldReceive('dispatch')->with('stream-start')->byDefault();
        $this->terminal->shouldReceive('dispatch')->with('stream-complete')->byDefault();
        $this->terminal->shouldReceive('formattedCommand')->byDefault();
        $this->terminal->formattedCommand = '';

        // Add expectations for the stream method
        $this->terminal->shouldReceive('stream')
             ->with('output', Mockery::any())
             ->byDefault();

        // Mock the WhoisFactory static method
        $this->whoisServer = Mockery::mock(Whois::class);
        $this->whois = Mockery::mock('alias:' . WhoisFactory::class);
        $this->whois->shouldReceive('get->createWhois')->andReturn($this->whoisServer);

        $this->command = new WhoisCommand();
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_command_name_and_description()
    {
        $this->assertEquals('whois', $this->command->getName());
        $this->assertEquals('Perform a whois lookup on a domain', $this->command->getDescription());
    }

    public function test_usage_message_when_no_domain_provided()
    {
        $output = $this->command->execute($this->terminal);

        $this->assertCount(1, $output);
        $this->assertStringContainsString('Usage: whois &lt;domain&gt;', $output[0]);
        $this->assertStringContainsString('text-red-400', $output[0]);
    }

    public function test_error_message_for_invalid_domain_format()
    {
        $this->whoisServer->shouldReceive('loadDomainInfo')->never();

        $output = $this->command->execute($this->terminal, ['invalid-domain-name']);

        // Debug output
        var_dump($output);

        $this->assertCount(1, $output);
        $this->assertStringContainsString('Invalid domain name format', $output[0]);
        $this->assertStringContainsString('text-red-400', $output[0]);
    }

    public function test_successful_whois_lookup()
    {
        $domainInfo = Mockery::mock(DomainInfo::class);
        $domainInfo->domainName = 'example.com';
        $domainInfo->states = ['active'];
        $domainInfo->expirationDate = strtotime('1970-01-01');
        $domainInfo->creationDate = strtotime('1992-01-01');
        $domainInfo->updatedDate = null;
        $domainInfo->registrar = 'Test Registrar';
        $domainInfo->nameServers = ['ns1.example.com', 'ns2.example.com'];
        $domainInfo->owner = 'Internet Assigned Numbers Authority';
        $domainInfo->registrant = null;
        $domainInfo->administrativeContact = null;
        $domainInfo->technicalContact = null;

        $this->whoisServer->shouldReceive('loadDomainInfo')
             ->once()
             ->with('example.com')
             ->andReturn($domainInfo);

        $output = $this->command->execute($this->terminal, ['example.com']);

        $this->assertStringContainsString('Performing whois lookup on example.com', $output[0]);
        $this->assertStringContainsString('Domain Information', implode('', $output));
        $this->assertStringContainsString('Domain: example.com', implode('', $output));
        $this->assertStringContainsString('Status: active', implode('', $output));
        $this->assertStringContainsString('Registrar: Test Registrar', implode('', $output));
        $this->assertStringContainsString('Name Servers: ns1.example.com, ns2.example.com', implode('', $output));
        $this->assertStringContainsString('Owner: Internet Assigned Numbers Authority', implode('', $output));
    }

    public function test_domain_availability_message()
    {
        $this->whoisServer->shouldReceive('loadDomainInfo')
             ->once()
             ->with('available.com')
             ->andReturnNull();

        $output = $this->command->execute($this->terminal, ['available.com']);

        $this->assertGreaterThan(1, count($output));
        $this->assertStringContainsString('Performing whois lookup on available.com', $output[0]);
        $this->assertStringContainsString('Domain is available!', implode('', $output));
        $this->assertStringContainsString('text-emerald-400', implode('', $output));
    }

    public function test_connection_exception_handling()
    {
        $this->whoisServer->shouldReceive('loadDomainInfo')
             ->once()
             ->with('example.com')
             ->andThrow(new ConnectionException());

        $output = $this->command->execute($this->terminal, ['example.com']);

        $this->assertGreaterThan(1, count($output));
        $this->assertStringContainsString('Performing whois lookup on example.com', $output[0]);
        $this->assertStringContainsString('Disconnect or connection timeout', implode('', $output));
        $this->assertStringContainsString('text-red-400', implode('', $output));
    }

    public function test_server_mismatch_exception_handling()
    {
        $this->whoisServer->shouldReceive('loadDomainInfo')
             ->once()
             ->with('example.com')
             ->andThrow(new ServerMismatchException());

        $output = $this->command->execute($this->terminal, ['example.com']);

        $this->assertGreaterThan(1, count($output));
        $this->assertStringContainsString('Performing whois lookup on example.com', $output[0]);
        $this->assertStringContainsString('TLD server not found in current server hosts', implode('', $output));
        $this->assertStringContainsString('text-red-400', implode('', $output));
    }

    public function test_whois_exception_handling()
    {
        $this->whoisServer->shouldReceive('loadDomainInfo')
             ->once()
             ->with('example.com')
             ->andThrow(new WhoisException('Test error message'));

        $output = $this->command->execute($this->terminal, ['example.com']);

        $this->assertGreaterThan(1, count($output));
        $this->assertStringContainsString('Performing whois lookup on example.com', $output[0]);
        $this->assertStringContainsString("Whois server responded with error 'Test error message'", implode('', $output));
        $this->assertStringContainsString('text-red-400', implode('', $output));
    }
}
