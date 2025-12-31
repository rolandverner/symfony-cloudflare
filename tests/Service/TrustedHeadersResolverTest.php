<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Tests\Service;

use Cloudflare\Proxy\Service\TrustedHeadersResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class TrustedHeadersResolverTest extends TestCase
{
    public function testResolveAll(): void
    {
        $resolver = new TrustedHeadersResolver([
            'x-forwarded-for',
            'x-forwarded-host',
            'x-forwarded-proto',
            'x-forwarded-port',
            'forwarded',
        ]);

        $expected = Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_PORT |
            Request::HEADER_FORWARDED;

        $this->assertEquals($expected, $resolver->resolve());
    }

    public function testResolvePartial(): void
    {
        $resolver = new TrustedHeadersResolver(['x-forwarded-for', 'x-forwarded-proto']);

        $expected = Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO;

        $this->assertEquals($expected, $resolver->resolve());
    }

    public function testResolveDefaultOnEmpty(): void
    {
        $resolver = new TrustedHeadersResolver([]);

        $expected = Request::HEADER_X_FORWARDED_FOR |
            Request::HEADER_X_FORWARDED_HOST |
            Request::HEADER_X_FORWARDED_PROTO |
            Request::HEADER_X_FORWARDED_PORT;

        $this->assertEquals($expected, $resolver->resolve());
    }
}
