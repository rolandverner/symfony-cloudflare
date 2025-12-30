<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CloudflareTrustedProxiesBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
