<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class AeliotTransMaintainExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('aeliot_trans_maintain.yaml.indent', $config['yaml']['indent']);
        $container->setParameter('aeliot_trans_maintain.insert_missed_keys', $config['insert_missed_keys']);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }
}
