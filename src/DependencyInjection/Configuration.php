<?php

namespace Aeliot\Bundle\TransMaintain\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('aeliot_trans_maintain');
        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('yaml')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('indent')->defaultValue(4)->cannotBeEmpty()->isRequired()->info('The amount of spaces to use for indentation of nested nodes')->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
