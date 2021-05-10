<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass;

use Aeliot\Bundle\TransMaintain\Service\LegacyTranslator;
use Aeliot\Bundle\TransMaintain\Service\Translator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Parameter;
use Symfony\Component\DependencyInjection\Reference;

final class TranslatorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if ($container->getParameter('aeliot_trans_maintain.insert_missed_keys') !== KeyRegister::NO) {
            if (!$container->has('translator.default')) {
                throw new \LogicException(
                    'Translator decorator cannot be enabled as the Translation component is not installed.'
                    .' Try running "composer require symfony/translation".'
                );
            }

            $translatorWrapper = new Definition($class = $this->getClass());
            $translatorWrapper->setDecoratedService('translator.default', 'translator.default.inner');
            $translatorWrapper->setAutowired(true);
            $translatorWrapper->setAutoconfigured(true);
            $translatorWrapper->setArgument('$decoratedTranslator', new Reference('translator.default.inner'));
            $translatorWrapper->setArgument('$position', new Parameter('aeliot_trans_maintain.insert_missed_keys'));
            $container->setDefinition($class, $translatorWrapper);
        }
    }

    private function getClass(): string
    {
        return \class_exists('Symfony\Component\Translation\TranslatorInterface')
            ? LegacyTranslator::class
            : Translator::class;
    }
}
