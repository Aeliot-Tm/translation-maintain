<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\FacadesRegistry;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('aeliot_trans_maintain');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } elseif (method_exists($treeBuilder, 'root')) {
            $rootNode = $treeBuilder->root('aeliot_trans_maintain');
        } else {
            throw new \LogicException('Cannot get root node');
        }

        $rootNode
            ->children()
                ->scalarNode('insert_missed_keys')
                    ->defaultValue(KeyRegister::NO)
                    ->validate()
                        ->ifNotInArray(KeyRegister::POSITIONS)
                        ->thenInvalid(
                            sprintf('Invalid configuration. Permitted keys are "%s" but there is another value %%s defined in the configuration.', implode('" ,"', KeyRegister::POSITIONS))
                        )
                    ->end()
                    ->info('Decorate default translator for inserting of missed keys.')
                ->end()
                ->arrayNode('linter')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('key_valid_pattern')->defaultNull()->info('Pattern to match keys. Example: /^[a-zA-Z0-9_.-]+$/')->end()
                        ->scalarNode('value_invalid_pattern')->defaultNull()->info('Pattern to match invalid value. Example: /[\x00-\x07]/')->end()
                    ->end()
                ->end()
                ->arrayNode('translation_api')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode(FacadesRegistry::FACADE_GOOGLE)
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('key')->defaultNull()->info('Google Cloud Translate key')->end()
                                ->scalarNode('limit')->defaultValue(500000)->info('Google Cloud Translate limit')->end()
                                ->scalarNode('model')->defaultValue('base')->info('Model used for translation')->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('yaml')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('indent')->defaultValue(4)->cannotBeEmpty()->isRequired()->info('The amount of spaces to use for indentation of nested nodes')->end()
                        ->scalarNode('key_pattern')->defaultNull()->info('Deprecated! Use: "linter: { key_valid_pattern: \'\' }"')->setDeprecated()->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
