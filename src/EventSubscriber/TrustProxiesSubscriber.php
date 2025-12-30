<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\EventSubscriber;

use Cloudflare\TrustedProxies\Service\CloudflareIpRepository;
use Cloudflare\TrustedProxies\Service\EnvTrustedProxiesResolver;
use Cloudflare\TrustedProxies\Service\TrustedHeadersResolver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class TrustProxiesSubscriber implements EventSubscriberInterface
{
    /**
     * @param string[] $extraProxies
     */
    public function __construct(
        private readonly CloudflareIpRepository $repository,
        private readonly EnvTrustedProxiesResolver $envResolver,
        private readonly TrustedHeadersResolver $headersResolver,
        private readonly string $mode = 'append',
        private readonly array $extraProxies = [],
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
        if (!$event->isMainRequest()) {
            return;
        }

        $cloudflareIps = $this->repository->getIps();
        $envIps = $this->envResolver->resolve();

        $trustedProxies = [];

        if ($this->mode === 'append') {
            $trustedProxies = array_unique(array_merge($envIps, $this->extraProxies, $cloudflareIps));
        } else {
            // override mode
            $trustedProxies = array_unique(array_merge($this->extraProxies, $cloudflareIps));
        }

        if (empty($trustedProxies)) {
            return;
        }

        Request::setTrustedProxies(
            $trustedProxies,
            $this->headersResolver->resolve()
        );
    }
}
