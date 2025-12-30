<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Service;

use Symfony\Component\HttpFoundation\Request;

class TrustedHeadersResolver
{
    private const MAP = [
        'x-forwarded-for' => Request::HEADER_X_FORWARDED_FOR,
        'x-forwarded-host' => Request::HEADER_X_FORWARDED_HOST,
        'x-forwarded-proto' => Request::HEADER_X_FORWARDED_PROTO,
        'x-forwarded-port' => Request::HEADER_X_FORWARDED_PORT,
        'forwarded' => Request::HEADER_FORWARDED,
    ];

    /**
     * @param string[] $headerNames
     */
    public function __construct(
        private readonly array $headerNames,
    ) {
    }

    public function resolve(): int
    {
        $bitmask = 0;

        foreach ($this->headerNames as $name) {
            $name = strtolower($name);
            if (isset(self::MAP[$name])) {
                $bitmask |= self::MAP[$name];
            }
        }

        return $bitmask ?: Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_HOST | Request::HEADER_X_FORWARDED_PROTO | Request::HEADER_X_FORWARDED_PORT;
    }
}
