<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\Service;

class EnvTrustedProxiesResolver
{
    private const FALLBACK_ENVS = ['TRUSTED_PROXIES', 'SYMFONY_TRUSTED_PROXIES'];

    public function __construct(
        private readonly ?string $proxiesEnv = null,
    ) {
    }

    /**
     * @return string[]
     */
    public function resolve(): array
    {
        $proxies = [];

        // 1. Try configured proxies_env
        if ($this->proxiesEnv && $envValue = $_ENV[$this->proxiesEnv] ?? $_SERVER[$this->proxiesEnv] ?? getenv($this->proxiesEnv)) {
            $proxies = $this->parse($envValue);
        }

        // 2. Try fallbacks if still empty
        if (empty($proxies)) {
            foreach (self::FALLBACK_ENVS as $envName) {
                if ($envValue = $_ENV[$envName] ?? $_SERVER[$envName] ?? getenv($envName)) {
                    $proxies = $this->parse($envValue);
                    if (!empty($proxies)) {
                        break;
                    }
                }
            }
        }

        return $proxies;
    }

    /**
     * @return string[]
     */
    private function parse(string $value): array
    {
        return array_filter(array_map('trim', explode(',', $value)));
    }
}
