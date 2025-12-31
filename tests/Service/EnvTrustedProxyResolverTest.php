<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Tests\Service;

use Cloudflare\Proxy\Service\EnvTrustedProxyResolver;
use PHPUnit\Framework\TestCase;

class EnvTrustedProxyResolverTest extends TestCase
{
    protected function tearDown(): void
    {
        unset($_SERVER['CUSTOM_PROXIES'], $_SERVER['TRUSTED_PROXIES'], $_SERVER['SYMFONY_TRUSTED_PROXIES']);
        putenv('CUSTOM_PROXIES');
        putenv('TRUSTED_PROXIES');
        putenv('SYMFONY_TRUSTED_PROXIES');
    }

    public function testResolveWithCustomEnv(): void
    {
        $_SERVER['CUSTOM_PROXIES'] = '10.0.0.1, 10.0.0.2';
        $resolver = new EnvTrustedProxyResolver('CUSTOM_PROXIES');

        $this->assertEquals(['10.0.0.1', '10.0.0.2'], $resolver->resolve());
    }

    public function testResolveWithTrustedProxiesFallback(): void
    {
        $_SERVER['TRUSTED_PROXIES'] = '127.0.0.1, ::1';
        $resolver = new EnvTrustedProxyResolver();

        $this->assertEquals(['127.0.0.1', '::1'], $resolver->resolve());
    }

    public function testResolveWithSymfonyTrustedProxiesFallback(): void
    {
        $_SERVER['SYMFONY_TRUSTED_PROXIES'] = '192.168.1.1';
        $resolver = new EnvTrustedProxyResolver();

        $this->assertEquals(['192.168.1.1'], $resolver->resolve());
    }

    public function testResolveEmpty(): void
    {
        $resolver = new EnvTrustedProxyResolver();
        $this->assertEquals([], $resolver->resolve());
    }
}
