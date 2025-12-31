<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Cloudflare\Proxy\Command\CloudflareInstallCommand;
use Cloudflare\Proxy\Command\CloudflareReloadCommand;
use Cloudflare\Proxy\Command\CloudflareViewCommand;
use Cloudflare\Proxy\EventSubscriber\TrustProxySubscriber;
use Cloudflare\Proxy\Service\CloudflareIpFetcher;
use Cloudflare\Proxy\Service\CloudflareIpRepository;
use Cloudflare\Proxy\Service\EnvTrustedProxyResolver;
use Cloudflare\Proxy\Service\TrustedHeadersResolver;
use Symfony\Component\Cache\Adapter\AdapterInterface;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->set(CloudflareIpFetcher::class);

    $services->set(CloudflareIpRepository::class)
        ->arg('$cacheKey', param('cloudflare_proxy.cache.key'))
        ->arg('$ttl', param('cloudflare_proxy.cache.ttl'));

    $services->set(EnvTrustedProxyResolver::class)
        ->arg('$proxyEnv', param('cloudflare_proxy.proxy_env'));

    $services->set(TrustedHeadersResolver::class)
        ->arg('$headerNames', param('cloudflare_proxy.trusted_headers'));

    $services->set(TrustProxySubscriber::class)
        ->arg('$enabled', param('cloudflare_proxy.enabled'))
        ->arg('$mode', param('cloudflare_proxy.mode'))
        ->arg('$extraProxy', param('cloudflare_proxy.extra'))
        ->tag('kernel.event_subscriber');

    $services->set(CloudflareInstallCommand::class)
        ->arg('$projectDir', param('kernel.project_dir'))
        ->tag('console.command');

    $services->set(CloudflareReloadCommand::class)
        ->tag('console.command');

    $services->set(CloudflareViewCommand::class)
        ->tag('console.command');
};
