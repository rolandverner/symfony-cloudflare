<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Contract;

interface CloudflareIpRepositoryInterface
{
    public function getIps(): array;

    public function refresh(): void;
}
