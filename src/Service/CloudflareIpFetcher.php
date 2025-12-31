<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Service;

use Cloudflare\Proxy\Contract\CloudflareIpFetcherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class CloudflareIpFetcher implements CloudflareIpFetcherInterface
{
    private const IPV4_URL = 'https://www.cloudflare.com/ips-v4';
    private const IPV6_URL = 'https://www.cloudflare.com/ips-v6';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    public function fetchIpv4(): array
    {
        return $this->fetch(self::IPV4_URL);
    }

    public function fetchIpv6(): array
    {
        return $this->fetch(self::IPV6_URL);
    }

    public function fetchAll(): array
    {
        return array_merge($this->fetchIpv4(), $this->fetchIpv6());
    }

    private function fetch(string $url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $content = $response->getContent();

        return array_filter(array_map('trim', explode("\n", $content)));
    }
}
