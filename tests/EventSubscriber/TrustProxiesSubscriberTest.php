<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Tests\EventSubscriber;

use Cloudflare\TrustedProxies\EventSubscriber\TrustProxiesSubscriber;
use Cloudflare\TrustedProxies\Service\CloudflareIpRepository;
use Cloudflare\TrustedProxies\Service\EnvTrustedProxiesResolver;
use Cloudflare\TrustedProxies\Service\TrustedHeadersResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TrustProxiesSubscriberTest extends TestCase
{
    protected function tearDown(): void
    {
        Request::setTrustedProxies([], -1);
    }

    public function testOnKernelRequestAppends(): void
    {
        $repository = $this->createMock(CloudflareIpRepository::class);
        $repository->method('getIps')->willReturn(['1.1.1.1']);

        $envResolver = $this->createMock(EnvTrustedProxiesResolver::class);
        $envResolver->method('resolve')->willReturn(['127.0.0.1']);

        $headersResolver = $this->createMock(TrustedHeadersResolver::class);
        $headersResolver->method('resolve')->willReturn(Request::HEADER_X_FORWARDED_FOR);

        $subscriber = new TrustProxiesSubscriber(
            $repository,
            $envResolver,
            $headersResolver,
            'append',
            ['10.0.0.1']
        );

        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST
        );

        $subscriber->onKernelRequest($event);

        $this->assertEquals(['127.0.0.1', '10.0.0.1', '1.1.1.1'], Request::getTrustedProxies());
        $this->assertEquals(Request::HEADER_X_FORWARDED_FOR, Request::getTrustedHeaderSet());
    }

    public function testOnKernelRequestOverrides(): void
    {
        $repository = $this->createMock(CloudflareIpRepository::class);
        $repository->method('getIps')->willReturn(['1.1.1.1']);

        $envResolver = $this->createMock(EnvTrustedProxiesResolver::class);
        $envResolver->method('resolve')->willReturn(['127.0.0.1']);

        $headersResolver = $this->createMock(TrustedHeadersResolver::class);
        $headersResolver->method('resolve')->willReturn(Request::HEADER_X_FORWARDED_FOR);

        $subscriber = new TrustProxiesSubscriber(
            $repository,
            $envResolver,
            $headersResolver,
            'override',
            ['10.0.0.1']
        );

        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST
        );

        $subscriber->onKernelRequest($event);

        $this->assertEquals(['10.0.0.1', '1.1.1.1'], Request::getTrustedProxies());
    }
}
