<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\EventSubscriber;

use Cloudflare\Proxy\Contract\CloudflareIpRepositoryInterface;
use Cloudflare\Proxy\Contract\EnvTrustedProxyResolverInterface;
use Cloudflare\Proxy\Contract\TrustedHeadersResolverInterface;
use Cloudflare\Proxy\Enum\ProxyMode;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class TrustProxySubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly CloudflareIpRepositoryInterface $repository,
        private readonly EnvTrustedProxyResolverInterface $envResolver,
        private readonly TrustedHeadersResolverInterface $headersResolver,
        private readonly bool $enabled = true,
        private readonly ProxyMode $mode = ProxyMode::APPEND,
        private readonly array $extraProxy = [],
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
                // High priority to run before security/firewall (Security uses priority 8)
            KernelEvents::REQUEST => ['onKernelRequest', 2000],
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if (!$this->enabled || !$event->isMainRequest()) {
            return;
        }

        $cloudflareIps = $this->repository->getIps();
        $envIps = $this->envResolver->resolve();

        $mergedIps = array_merge($envIps, $this->extraProxy, $cloudflareIps);

        if ($this->mode === ProxyMode::OVERRIDE) {
            $mergedIps = array_merge($this->extraProxy, $cloudflareIps);
        }

        $trustedProxy = array_unique($mergedIps);

        if ([] === $trustedProxy) {
            return;
        }

        Request::setTrustedProxies(
            $trustedProxy,
            $this->headersResolver->resolve()
        );
    }
}
