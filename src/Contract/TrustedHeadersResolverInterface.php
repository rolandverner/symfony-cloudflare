<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Contract;

interface TrustedHeadersResolverInterface
{
    public function resolve(): int;
}
