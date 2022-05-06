<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\FacadesRegistry;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
use Symfony\Component\Config\Definition\Builder\NodeBuilder;
use Symfony\Component\Config\Definition\Builder\NodeParentInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('aeliot_trans_maintain');

        if (method_exists($treeBuilder, 'getRootNode')) {
            /** @var NodeParentInterface $rootNode */
            $rootNode = $treeBuilder->getRootNode();
        } elseif (method_exists($treeBuilder, 'root')) {
            $rootNode = $treeBuilder->root('aeliot_trans_maintain');
        } else {
            throw new \LogicException('Cannot get root node');
        }

        /* @var NodeBuilder $builder */
        $builder = $rootNode->children();
        $this->addInsertMissedKeysNode($builder);
        $this->addLinterNode($builder);
        $this->addTranslationAPINode($builder);
        $this->addYAMLNode($builder);

        return $treeBuilder;
    }

    private function addGoogleTranslationAPINode(NodeBuilder $builder): void
    {
        $children = $builder
            ->arrayNode(FacadesRegistry::FACADE_GOOGLE)
            ->addDefaultsIfNotSet()
            ->children();

        $children
            ->scalarNode('key')
            ->defaultNull()
            ->info('Google Cloud Translate key');

        $children
            ->scalarNode('limit')
            ->defaultValue(500000)
            ->info('Google Cloud Translate limit');

        $children
            ->scalarNode('model')
            ->defaultValue('base')
            ->info('Model used for translation');
    }

    private function addInsertMissedKeysNode(NodeBuilder $builder): void
    {
        $this->addInsertMissedKeysPositionNode($builder, 'insert_missed_keys', true);

        $children = $builder
            ->arrayNode('missed_keys')
            ->addDefaultsIfNotSet()
            ->children();

        $this->addInsertMissedKeysPositionNode($children, 'insert_position', false);

        $children
            ->scalarNode('directory')
            ->defaultNull()
            ->info('Directory for the separate saving of missed translation IDs');
    }

    private function addInsertMissedKeysPositionNode(NodeBuilder $builder, string $name, bool $deprecated): void
    {
        $node = $builder
            ->scalarNode($name)
            ->defaultNull()
            ->info('Decorate default translator for inserting of missed keys.');

        if ($deprecated) {
            $node->setDeprecated('aeliot-tm/translation-maintain', '2.7.0', 'Use: "missed_keys: { insert_position: \'\' }"');
        }

        $node
            ->validate()
            ->ifNotInArray(KeyRegister::POSITIONS)
            ->thenInvalid(sprintf('Invalid configuration. Permitted keys are "%s" but there is another value %%s defined in the configuration.', implode('" ,"', KeyRegister::POSITIONS)));
    }

    private function addLinterNode(NodeBuilder $builder): void
    {
        $children = $builder
            ->arrayNode('linter')
            ->addDefaultsIfNotSet()
            ->children();

        $children
            ->scalarNode('key_valid_pattern')
            ->defaultNull()
            ->info('Pattern to match keys. Example: /^[a-zA-Z0-9_.-]+$/');

        $children
            ->scalarNode('value_invalid_pattern')
            ->defaultNull()
            ->info('Pattern to match invalid value. Example: /[\x00-\x07]/');
    }

    private function addTranslationAPINode(NodeBuilder $builder): void
    {
        $children = $builder
            ->arrayNode('translation_api')
            ->addDefaultsIfNotSet()
            ->children();

        $this->addGoogleTranslationAPINode($children);
    }

    private function addYAMLNode(NodeBuilder $builder): void
    {
        $children = $builder
            ->arrayNode('yaml')
            ->addDefaultsIfNotSet()
            ->children();

        $children
            ->scalarNode('indent')
            ->defaultValue(4)
            ->cannotBeEmpty()
            ->isRequired()
            ->info('The amount of spaces to use for indentation of nested nodes');

        $children
            ->scalarNode('key_pattern')
            ->defaultNull()
            ->info('Pattern to match keys. Example: /^[a-zA-Z0-9_.-]+$/')
            ->setDeprecated('aeliot-tm/translation-maintain', '2.7.0', 'Use: "linter: { key_valid_pattern: \'\' }"');
    }
}
