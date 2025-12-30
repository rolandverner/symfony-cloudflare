<?php

declare(strict_types=1);

namespace Cloudflare\TrustedProxies\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cloudflare_proxies');

        $treeBuilder->getRootNode()
            ->children()
            ->enumNode('mode')
            ->values(['append', 'override'])
            ->defaultValue('append')
            ->info('How to merge Cloudflare IPs with existing trusted proxies.')
            ->end()
            ->scalarNode('proxies_env')
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
            ->defaultValue('cloudflare_proxies_ips')
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
