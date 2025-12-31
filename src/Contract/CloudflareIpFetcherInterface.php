<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Contract;

interface CloudflareIpFetcherInterface
{
    public function fetchIpv4(): array;

    public function fetchIpv6(): array;

    public function fetchAll(): array;
}
