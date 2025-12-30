<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Service;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CloudflareIpRepository
{
    public function __construct(
        private readonly CloudflareIpFetcher $fetcher,
        private readonly CacheInterface $cache,
        private readonly string $cacheKey,
        private readonly int $ttl,
    ) {
    }

    /**
     * @return string[]
     */
    public function getIps(): array
    {
        return $this->cache->get($this->cacheKey, function (ItemInterface $item) {
            $item->expiresAfter($this->ttl);
            return $this->fetcher->fetchAll();
        });
    }

    public function refresh(): void
    {
        $this->cache->delete($this->cacheKey);
        $this->getIps();
    }
}
