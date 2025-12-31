<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Tests\EventSubscriber;

use Cloudflare\Proxy\Contract\CloudflareIpRepositoryInterface;
use Cloudflare\Proxy\Contract\EnvTrustedProxyResolverInterface;
use Cloudflare\Proxy\Contract\TrustedHeadersResolverInterface;
use Cloudflare\Proxy\Enum\ProxyMode;
use Cloudflare\Proxy\EventSubscriber\TrustProxySubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class TrustProxySubscriberTest extends TestCase
{
    protected function tearDown(): void
    {
        Request::setTrustedProxies([], -1);
    }

    public function testOnKernelRequestAppends(): void
    {
        $repository = $this->createMock(CloudflareIpRepositoryInterface::class);
        $repository->method('getIps')->willReturn(['1.1.1.1']);

        $envResolver = $this->createMock(EnvTrustedProxyResolverInterface::class);
        $envResolver->method('resolve')->willReturn(['127.0.0.1']);

        $headersResolver = $this->createMock(TrustedHeadersResolverInterface::class);
        $headersResolver->method('resolve')->willReturn(Request::HEADER_X_FORWARDED_FOR);

        $subscriber = new TrustProxySubscriber(
            $repository,
            $envResolver,
            $headersResolver,
            true,
            ProxyMode::APPEND,
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
        $repository = $this->createMock(CloudflareIpRepositoryInterface::class);
        $repository->method('getIps')->willReturn(['1.1.1.1']);

        $envResolver = $this->createMock(EnvTrustedProxyResolverInterface::class);
        $envResolver->method('resolve')->willReturn(['127.0.0.1']);

        $headersResolver = $this->createMock(TrustedHeadersResolverInterface::class);
        $headersResolver->method('resolve')->willReturn(Request::HEADER_X_FORWARDED_FOR);

        $subscriber = new TrustProxySubscriber(
            $repository,
            $envResolver,
            $headersResolver,
            true,
            ProxyMode::OVERRIDE,
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

    public function testOnKernelRequestDoesNothingWhenDisabled(): void
    {
        $repository = $this->createMock(CloudflareIpRepositoryInterface::class);
        $repository->expects($this->never())->method('getIps');

        $envResolver = $this->createMock(EnvTrustedProxyResolverInterface::class);
        $headersResolver = $this->createMock(TrustedHeadersResolverInterface::class);

        $subscriber = new TrustProxySubscriber(
            $repository,
            $envResolver,
            $headersResolver,
            false // disabled
        );

        $event = new RequestEvent(
            $this->createMock(HttpKernelInterface::class),
            new Request(),
            HttpKernelInterface::MAIN_REQUEST
        );

        $subscriber->onKernelRequest($event);

        $this->assertEquals([], Request::getTrustedProxies());
    }
}
