<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass;

use Aeliot\Bundle\TransMaintain\Service\KernelVersionDetector;
use Aeliot\Bundle\TransMaintain\Service\Translator\TranslatorV3P4;
use Aeliot\Bundle\TransMaintain\Service\Translator\TranslatorV5P0;
use Aeliot\Bundle\TransMaintain\Service\Translator\TranslatorV6P0;
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
        if (KeyRegister::NO === $container->getParameter('aeliot_trans_maintain.insert_missed_keys')) {
            return;
        }

        if (!$container->has('translator.default')) {
            $message = 'Translator decorator cannot be enabled as the Translation component is not installed.'
                .' Try running "composer require symfony/translation".';
            throw new \LogicException($message);
        }

        $translatorWrapper = new Definition($class = $this->getClass($container));
        $translatorWrapper->setDecoratedService('translator.default', 'translator.default.inner');
        $translatorWrapper->setAutowired(true);
        $translatorWrapper->setAutoconfigured(true);
        $translatorWrapper->setArgument('$decoratedTranslator', new Reference('translator.default.inner'));
        $translatorWrapper->setArgument('$translationReader', new Reference('translation.reader'));
        $translatorWrapper->setArgument('$position', new Parameter('aeliot_trans_maintain.insert_missed_keys'));
        $translatorWrapper->setArgument(
            '$separateDirectory',
            new Parameter('aeliot_trans_maintain.missed_keys.directory')
        );
        $container->setDefinition($class, $translatorWrapper);
    }

    private function getClass(ContainerBuilder $container): string
    {
        $version = (new KernelVersionDetector())->getVersion($container, 'symfony/translation');
        if (version_compare($version, '5.0.0', '<')) {
            return TranslatorV3P4::class;
        }
        if (version_compare($version, '6.0.0', '<')) {
            return TranslatorV5P0::class;
        }

        return TranslatorV6P0::class;
    }
}
