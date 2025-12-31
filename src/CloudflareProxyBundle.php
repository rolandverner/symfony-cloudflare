<?php

declare(strict_types=1);

namespace Cloudflare\Proxy;

use Cloudflare\Proxy\DependencyInjection\CloudflareProxyExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class CloudflareProxyBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }

    public function getContainerExtension(): ?ExtensionInterface
    {
        if (null === $this->extension) {
            $this->extension = new CloudflareProxyExtension();
        }

        return $this->extension;
    }
}