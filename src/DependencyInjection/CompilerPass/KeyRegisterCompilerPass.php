<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class KeyRegisterCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $reader = $container->getDefinition(KeyRegister::class);
        $reader->replaceArgument('$dirs', $this->getDirectories($container));
    }

    private function getDirectories(ContainerBuilder $container): array
    {
        $dirs = [];
        $rootDir = $container->getParameter('kernel.root_dir');
        $dirs[] = $container->getParameter('translator.default_path');
        $dirs[] = $rootDir.'/translations';
        $dirs[] = $rootDir.'/Resources/translations';
        $vendorDir = $rootDir.'/vendor';

        foreach ($container->getParameter('kernel.bundles_metadata') as $bundle) {
            if (($container->fileExists($dir = $bundle['path'].'/Resources/translations')
                    || $container->fileExists($dir = $bundle['path'].'/translations'))
                && stripos($dir, $vendorDir) === false
            ) {
                $dirs[] = $dir;
            }
        }

        return array_unique($dirs);
    }
}
