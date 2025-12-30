<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Tests\Service;

use Cloudflare\TrustedProxies\Service\EnvTrustedProxiesResolver;
use PHPUnit\Framework\TestCase;

class EnvTrustedProxiesResolverTest extends TestCase
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
        $resolver = new EnvTrustedProxiesResolver('CUSTOM_PROXIES');

        $this->assertEquals(['10.0.0.1', '10.0.0.2'], $resolver->resolve());
    }

    public function testResolveWithTrustedProxiesFallback(): void
    {
        $_SERVER['TRUSTED_PROXIES'] = '127.0.0.1, ::1';
        $resolver = new EnvTrustedProxiesResolver();

        $this->assertEquals(['127.0.0.1', '::1'], $resolver->resolve());
    }

    public function testResolveWithSymfonyTrustedProxiesFallback(): void
    {
        $_SERVER['SYMFONY_TRUSTED_PROXIES'] = '192.168.1.1';
        $resolver = new EnvTrustedProxiesResolver();

        $this->assertEquals(['192.168.1.1'], $resolver->resolve());
    }

    public function testResolveEmpty(): void
    {
        $resolver = new EnvTrustedProxiesResolver();
        $this->assertEquals([], $resolver->resolve());
    }
}
