<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service;

use Symfony\Component\DependencyInjection\ContainerBuilder;

final class KernelVersionDetector
{
    public function getVersion(ContainerBuilder $container, string $packageName): string
    {
        if (($path = $this->locateComposerLockFile($container))
            && ($version = $this->getVersionFromComposer($path, $packageName))
        ) {
            return $version;
        }

        // try to guess
        if ($container->hasParameter('kernel.project_dir') && !$container->hasParameter('kernel.root_dir')) {
            return '5.0.0';
        }
        if ($container->hasParameter('kernel.root_dir')) {
            return '4.4.0';
        }

        throw new \LogicException('Impossible to define version of kernel');
    }

    private function getVersionFromComposer(string $path, string $packageName): ?string
    {
        $packages = [];
        if (file_exists($path)) {
            $packages = (json_decode(file_get_contents($path), true, 512, \JSON_THROW_ON_ERROR)['packages'] ?? []);
            $packages = array_filter($packages, static fn (array $x): bool => $x['name'] === $packageName);
        }

        if ($packages && preg_match('/v?(\d+(?:\.\d+){,2})/', end($packages)['version'] ?? '', $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function locateComposerLockFile(ContainerBuilder $container): ?string
    {
        if ($container->hasParameter('kernel.project_dir')
            && ($projectPath = $container->getParameter('kernel.project_dir'))
            && file_exists($path = (rtrim($projectPath, '\\/').\DIRECTORY_SEPARATOR.'composer.lock'))
        ) {
            return $path;
        }

        if ($path = realpath(__DIR__.'/../../../../../composer.lock')) {
            return $path;
        }

        return null;
    }
}
