<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class CloudflareIpFetcher
{
    private const IPV4_URL = 'https://www.cloudflare.com/ips-v4';
    private const IPV6_URL = 'https://www.cloudflare.com/ips-v6';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
    ) {
    }

    /**
     * @return string[]
     */
    public function fetchIpv4(): array
    {
        return $this->fetch(self::IPV4_URL);
    }

    /**
     * @return string[]
     */
    public function fetchIpv6(): array
    {
        return $this->fetch(self::IPV6_URL);
    }

    /**
     * @return string[]
     */
    public function fetchAll(): array
    {
        return array_merge($this->fetchIpv4(), $this->fetchIpv6());
    }

    /**
     * @return string[]
     */
    private function fetch(string $url): array
    {
        $response = $this->httpClient->request('GET', $url);
        $content = $response->getContent();

        return array_filter(array_map('trim', explode("\n", $content)));
    }
}
