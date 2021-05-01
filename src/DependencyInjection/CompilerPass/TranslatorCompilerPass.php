<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass;

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
            $translatorWrapper = new Definition(Translator::class);
            $translatorWrapper->setDecoratedService('translator.default', 'translator.default.inner');
            $translatorWrapper->setAutowired(true);
            $translatorWrapper->setAutoconfigured(true);
            $translatorWrapper->setArgument('$decoratedTranslator', new Reference('translator.default.inner'));
            $translatorWrapper->setArgument('$position', new Parameter('aeliot_trans_maintain.insert_missed_keys'));
            $container->setDefinition(Translator::class, $translatorWrapper);
        }
    }
}
