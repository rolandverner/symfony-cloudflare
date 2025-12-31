<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Service;

use Cloudflare\Proxy\Contract\CloudflareIpFetcherInterface;
use Cloudflare\Proxy\Contract\CloudflareIpRepositoryInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

final class CloudflareIpRepository implements CloudflareIpRepositoryInterface
{
    public function __construct(
        private readonly CloudflareIpFetcherInterface $fetcher,
        private readonly CacheInterface $cache,
        private readonly string $cacheKey,
        private readonly int $ttl,
    ) {
    }

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
