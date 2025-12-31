<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\DependencyInjection;

use Cloudflare\Proxy\Enum\ProxyMode;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cloudflare_proxy');

        $treeBuilder->getRootNode()
            ->children()
            ->booleanNode('enabled')
            ->defaultTrue()
            ->info('Whether to enable the Cloudflare trusted proxies listener.')
            ->end()
            ->enumNode('mode')
            ->values(array_map(fn(ProxyMode $m) => $m->value, ProxyMode::cases()))
            ->defaultValue(ProxyMode::APPEND->value)
            ->info('How to merge Cloudflare IPs with existing trusted proxies.')
            ->end()
            ->scalarNode('proxy_env')
            ->defaultNull()
            ->info('Optional custom environment variable name for trusted proxies.')
            ->end()
            ->arrayNode('extra')
            ->scalarPrototype()->end()
            ->defaultValue([])
            ->info('Additional IP ranges to trust.')
            ->end()
            ->arrayNode('trusted_headers')
            ->scalarPrototype()->end()
            ->defaultValue(['x-forwarded-for', 'x-forwarded-host', 'x-forwarded-proto', 'x-forwarded-port', 'forwarded'])
            ->info('Headers to trust from Cloudflare.')
            ->end()
            ->arrayNode('cache')
            ->addDefaultsIfNotSet()
            ->children()
            ->scalarNode('pool')
            ->defaultValue('cache.app')
            ->end()
            ->scalarNode('key')
            ->defaultValue('cloudflare_proxies.ips')
            ->end()
            ->integerNode('ttl')
            ->defaultValue(86400)
            ->end()
            ->end()
            ->end()
            ->end();

        return $treeBuilder;
    }
}
