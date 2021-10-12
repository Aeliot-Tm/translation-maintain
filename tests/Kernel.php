<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test;

use Aeliot\Bundle\TransMaintain\AeliotTransMaintainBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as SymfonyKernel;

final class Kernel extends SymfonyKernel
{
    private const CONFIG_EXTENSIONS = '.{php,xml,yaml,yml}';

    /**
     * @return BundleInterface[]
     */
    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new AeliotTransMaintainBundle(),
        ];
    }

    public function getRootDir(): string
    {
        return __DIR__;
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/Aeliot/TransMaintain/cache';
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/Aeliot/TransMaintain/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__.'/config/packages/*'.self::CONFIG_EXTENSIONS, 'glob');
        $loader->load(__DIR__.'/config/services'.self::CONFIG_EXTENSIONS, 'glob');
    }

    protected function getContainerClass(): string
    {
        return parent::getContainerClass().'Tmp';
    }
}
