<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class CloudflareTrustedProxiesExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $container->setParameter('cloudflare_proxies.mode', $config['mode']);
        $container->setParameter('cloudflare_proxies.proxies_env', $config['proxies_env']);
        $container->setParameter('cloudflare_proxies.extra', $config['extra']);
        $container->setParameter('cloudflare_proxies.trusted_headers', $config['trusted_headers']);
        $container->setParameter('cloudflare_proxies.cache.pool', $config['cache']['pool']);
        $container->setParameter('cloudflare_proxies.cache.key', $config['cache']['key']);
        $container->setParameter('cloudflare_proxies.cache.ttl', $config['cache']['ttl']);
    }

    public function getAlias(): string
    {
        return 'cloudflare_proxies';
    }
}
