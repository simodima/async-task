<?php

namespace Trt\AsyncTasksBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('trt_async_tasks');

        $rootNode
            ->children()
                ->arrayNode('event')
                    ->children()
                        ->scalarNode('prefix')->defaultValue('async')->end()
                    ->end()
                ->end()
                ->arrayNode('mq')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('client_class')
                            ->defaultValue('Trt\AsyncTasksBundle\Service\RabbitMq\RabbitMqService')
                        ->end()
                        ->arrayNode('connection_params')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->arrayNode('exchange')
                            ->useAttributeAsKey('name')
                            ->prototype('scalar')
                            ->end()
                        ->end()
                        ->scalarNode('host')->defaultValue('localhost')->end()
                        ->scalarNode('port')->defaultValue('')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
