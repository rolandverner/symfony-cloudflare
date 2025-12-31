<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Contract;

interface EnvTrustedProxyResolverInterface
{
    public function resolve(): array;
}
