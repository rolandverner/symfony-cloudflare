<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Cloudflare\TrustedProxies\Command\CloudflareReloadCommand;
use Cloudflare\TrustedProxies\Command\CloudflareViewCommand;
use Cloudflare\TrustedProxies\EventSubscriber\TrustProxiesSubscriber;
use Cloudflare\TrustedProxies\Service\CloudflareIpFetcher;
use Cloudflare\TrustedProxies\Service\CloudflareIpRepository;
use Cloudflare\TrustedProxies\Service\EnvTrustedProxiesResolver;
use Cloudflare\TrustedProxies\Service\TrustedHeadersResolver;
use Symfony\Component\Cache\Adapter\AdapterInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CloudflareIpFetcher::class);

    $services->set(CloudflareIpRepository::class)
        ->arg('$cache', service('%cloudflare_proxies.cache.pool%'))
        ->arg('$cacheKey', param('cloudflare_proxies.cache.key'))
        ->arg('$ttl', param('cloudflare_proxies.cache.ttl'));

    $services->set(EnvTrustedProxiesResolver::class)
        ->arg('$proxiesEnv', param('cloudflare_proxies.proxies_env'));

    $services->set(TrustedHeadersResolver::class)
        ->arg('$headerNames', param('cloudflare_proxies.trusted_headers'));

    $services->set(TrustProxiesSubscriber::class)
        ->arg('$mode', param('cloudflare_proxies.mode'))
        ->arg('$extraProxies', param('cloudflare_proxies.extra'))
        ->tag('kernel.event_subscriber');

    $services->set(CloudflareReloadCommand::class)
        ->tag('console.command');

    $services->set(CloudflareViewCommand::class)
        ->tag('console.command');
};
