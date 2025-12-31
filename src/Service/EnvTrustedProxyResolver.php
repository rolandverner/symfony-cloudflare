<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Service;

use Cloudflare\Proxy\Contract\EnvTrustedProxyResolverInterface;

final class EnvTrustedProxyResolver implements EnvTrustedProxyResolverInterface
{
    private const FALLBACK_ENVS = ['TRUSTED_PROXIES', 'SYMFONY_TRUSTED_PROXIES'];

    public function __construct(
        private readonly ?string $proxyEnv = null,
    ) {
    }

    public function resolve(): array
    {
        if ($this->proxyEnv) {
            $value = $this->getEnv($this->proxyEnv);
            if (null !== $value) {
                return $this->parse($value);
            }
        }

        foreach (self::FALLBACK_ENVS as $envName) {
            $value = $this->getEnv($envName);
            if (null !== $value) {
                $proxies = $this->parse($value);
                if ([] !== $proxies) {
                    return $proxies;
                }
            }
        }

        return [];
    }

    private function getEnv(string $name): ?string
    {
        if (isset($_ENV[$name])) {
            return (string) $_ENV[$name];
        }

        if (isset($_SERVER[$name])) {
            return (string) $_SERVER[$name];
        }

        $value = getenv($name);

        return false !== $value ? (string) $value : null;
    }

    /**
     * @return string[]
     */
    private function parse(string $value): array
    {
        return array_filter(array_map('trim', explode(',', $value)));
    }
}
