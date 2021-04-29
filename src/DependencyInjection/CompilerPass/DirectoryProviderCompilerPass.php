<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass;

use Aeliot\Bundle\TransMaintain\Service\DirectoryProvider;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class DirectoryProviderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $reader = $container->getDefinition(DirectoryProvider::class);
        $reader->replaceArgument('$dirs', $this->getDirectories($container));
    }

    /**
     * @return array<string>
     */
    private function getDirectories(ContainerBuilder $container): array
    {
        $dirs = [];
        $rootDir = $container->getParameter('kernel.root_dir');
        $projectDir = \dirname($rootDir);
        $dirs[] = $projectDir.'/translations';
        $dirs[] = $rootDir.'/app/Resources/translations';
        $dirs[] = $rootDir.'/Resources/translations';
        $vendorDir = $projectDir.'/vendor';

        foreach ($container->getParameter('kernel.bundles_metadata') as $bundle) {
            if (($container->fileExists($dir = $bundle['path'].'/Resources/translations')
                    || $container->fileExists($dir = $bundle['path'].'/translations'))
                && stripos($dir, $vendorDir) === false
            ) {
                $dirs[] = $dir;
            }
        }

        return array_filter(array_unique($dirs), static fn (string $path): bool => $container->fileExists($path));
    }
}
