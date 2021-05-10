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
        if (version_compare($this->getKernelVersion($container), '5.0.0', '>=')) {
            $projectDir = $container->hasParameter('kernel.project_dir');
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

    private function getKernelVersion(ContainerBuilder $container): string
    {
        if (($path = $this->locateComposerLockFile($container)) && ($version = $this->getVersionFromComposer($path))) {
            return $version;
        }

        if ($container->hasParameter('kernel.project_dir') && !$container->hasParameter('kernel.root_dir')) {
            return '5.0.0';
        }
        if ($container->hasParameter('kernel.root_dir')) {
            return '4.4.0';
        }

        throw new \LogicException('Impossible to define version of kernel');
    }

    private function getVersionFromComposer(string $path): ?string
    {
        $packages = [];
        if (file_exists($path)) {
            $packages = (\json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR)['packages'] ?? []);
            $packages = array_filter($packages, static fn(array $x): bool => $x['name'] === 'symfony/http-kernel');
        }

        if ($packages && preg_match('/v?(\d+(?:\.\d+){,2})/', end($packages)['version'] ?? '', $matches)) {
            return $matches[1];
        }

        return null;
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

    private function locateComposerLockFile(ContainerBuilder $container): ?string
    {
        if ($container->hasParameter('kernel.project_dir')
            && ($projectPath = $container->getParameter('kernel.project_dir'))
            && file_exists($path = (rtrim($projectPath, '\\/').DIRECTORY_SEPARATOR.'composer.lock'))
        ) {
            return $path;
        }

        if ($path = realpath(__DIR__.'/../../../../../../composer.lock')) {
            return $path;
        }

        return null;
    }
}
