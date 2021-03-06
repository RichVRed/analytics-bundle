<?php

namespace Revinate\AnalyticsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;


/**
 * Class Configuration
 * @package Revinate\RabbitMqBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('revinate_analytics');
        $rootNode
            ->children()
                ->arrayNode('connection')
                    ->children()
                        ->scalarNode('host')->end()
                        ->scalarNode('port')->end()
                    ->end()
                ->end()
                ->arrayNode('connections')
                    ->useAttributeAsKey('key')
                    ->canBeUnset()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('host')->end()
                            ->scalarNode('port')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('sources')
                    ->useAttributeAsKey('key')
                    ->canBeUnset()
                    ->prototype('array')
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('connection')->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('api')
                    ->children()
                        ->scalarNode('path')->end()
                    ->end()
                ->end()
            ->end()
        ;
        return $treeBuilder;
    }
}
