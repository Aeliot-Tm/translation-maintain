<?php

namespace Aeliot\Bundle\TransMaintain\DependencyInjection;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
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
                ->scalarNode('insert_missed_keys')
                    ->defaultValue(KeyRegister::NO)
                    ->validate()
                        ->ifNotInArray(KeyRegister::POSITIONS)
                        ->thenInvalid(
                            \sprintf('Invalid configuration. Permitted keys: "%s"', implode('" ,"', KeyRegister::POSITIONS))
                        )
                    ->end()
                    ->info('Decorate default translator for inserting of missed keys.')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
