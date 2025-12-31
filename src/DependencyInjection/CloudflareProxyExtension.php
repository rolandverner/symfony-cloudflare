<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Cloudflare\Proxy\Enum\ProxyMode;
use Cloudflare\Proxy\Service\CloudflareIpRepository;

class CloudflareProxyExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.php');

        $container->setParameter('cloudflare_proxy.enabled', $config['enabled']);
        $container->setParameter('cloudflare_proxy.mode', ProxyMode::from($config['mode']));
        $container->setParameter('cloudflare_proxy.proxy_env', $config['proxy_env']);
        $container->setParameter('cloudflare_proxy.extra', $config['extra']);
        $container->setParameter('cloudflare_proxy.trusted_headers', $config['trusted_headers']);
        $container->setParameter('cloudflare_proxy.cache.pool', $config['cache']['pool']);
        $container->setParameter('cloudflare_proxy.cache.key', $config['cache']['key']);
        $container->setParameter('cloudflare_proxy.cache.ttl', $config['cache']['ttl']);

        $container->getDefinition(CloudflareIpRepository::class)
            ->setArgument('$cache', new Reference($config['cache']['pool']));
    }

    public function getAlias(): string
    {
        return 'cloudflare_proxy';
    }
}
