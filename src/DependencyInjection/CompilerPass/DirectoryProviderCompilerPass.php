<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass;

use Aeliot\Bundle\TransMaintain\Service\DirectoryProvider;
use Aeliot\Bundle\TransMaintain\Service\KernelVersionDetector;
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
        $version = (new KernelVersionDetector())->getVersion($container, 'symfony/http-kernel');
        if (version_compare($version, '5.0.0', '>=')) {
            $projectDir = $container->getParameter('kernel.project_dir');
            $dirs[] = $projectDir.'/translations';
            $isResourcesFirst = false;
        } else {
            $rootDir = $container->getParameter('kernel.root_dir');
            $projectDir = \dirname($rootDir);
            $dirs[] = $projectDir.'/translations';
            $dirs[] = $rootDir.'/Resources/translations';
            $dirs[] = $projectDir.'/app/Resources/translations';
            $isResourcesFirst = true;
        }
        $vendorDir = $projectDir.'/vendor';

        foreach ($container->getParameter('kernel.bundles_metadata') as $bundle) {
            $dir = $this->locateBundlePath($container, $bundle['path'], $isResourcesFirst);
            if ($dir && stripos($dir, $vendorDir) === false) {
                $dirs[] = $dir;
            }
        }

        return array_filter(array_unique($dirs), static fn (string $path): bool => $container->fileExists($path));
    }

    private function locateBundlePath(ContainerBuilder $container, string $path, bool $isResourcesFirst): ?string
    {
        if ($isResourcesFirst) {
            if ($container->fileExists($dir = $path.'/Resources/translations')
                || $container->fileExists($dir = $path.'/translations')
            ) {
                return $dir;
            }
        } elseif ($container->fileExists($dir = $path.'/translations')) {
            return $dir;
        }

        return null;
    }
}
