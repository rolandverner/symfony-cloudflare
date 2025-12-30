<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Tests\Service;

use Cloudflare\TrustedProxies\Service\CloudflareIpFetcher;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class CloudflareIpFetcherTest extends TestCase
{
    public function testFetchAll(): void
    {
        $httpClient = $this->createMock(HttpClientInterface::class);
        $responseV4 = $this->createMock(ResponseInterface::class);
        $responseV6 = $this->createMock(ResponseInterface::class);

        $responseV4->method('getContent')->willReturn("1.1.1.1/32\n2.2.2.2/24");
        $responseV6->method('getContent')->willReturn("2400:cb00::/32\n2606:4700::/32");

        $httpClient->expects($this->exactly(2))
            ->method('request')
            ->willReturnMap([
                ['GET', 'https://www.cloudflare.com/ips-v4', [], $responseV4],
                ['GET', 'https://www.cloudflare.com/ips-v6', [], $responseV6],
            ]);

        $fetcher = new CloudflareIpFetcher($httpClient);
        $ips = $fetcher->fetchAll();

        $this->assertEquals([
            '1.1.1.1/32',
            '2.2.2.2/24',
            '2400:cb00::/32',
            '2606:4700::/32',
        ], $ips);
    }
}
