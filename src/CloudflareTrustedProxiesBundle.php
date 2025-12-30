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

    public function getContainerExtension(): ?\Symfony\Component\DependencyInjection\Extension\ExtensionInterface
    {
        return new \Cloudflare\TrustedProxies\DependencyInjection\CloudflareTrustedProxiesExtension();
    }
}
