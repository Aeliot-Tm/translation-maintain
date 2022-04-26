<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\DependencyInjection;

use Aeliot\Bundle\TransMaintain\Service\ApiTranslator\FacadesRegistry;
use Aeliot\Bundle\TransMaintain\Service\KernelVersionDetector;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

final class AeliotTransMaintainExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('aeliot_trans_maintain.linter.value.invalid_pattern', $config['linter']['value_invalid_pattern'] ?? null);
        $container->setParameter('aeliot_trans_maintain.yaml.indent', $config['yaml']['indent']);
        $container->setParameter('aeliot_trans_maintain.yaml.key_pattern', $config['linter']['key_valid_pattern'] ?? $config['yaml']['key_pattern'] ?? null);
        $container->setParameter('aeliot_trans_maintain.insert_missed_keys', $config['insert_missed_keys']);

        $this->defineGoogleCloudTranslate($config, $container);
        $this->defineTranslationApiParameters($config['translation_api'] ?? [], $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yaml');
    }

    private function defineGoogleCloudTranslate(array $config, ContainerBuilder $container): void
    {
        $google = $config['translation_api'][FacadesRegistry::FACADE_GOOGLE];
        $clientConfig = ['key' => $google['key'] ?? null];
        $container->setParameter('aeliot_trans_maintain.translation_api.google.config', $clientConfig);
        $container->setParameter('aeliot_trans_maintain.translation_api.google.model', $google['model'] ?? null);
    }

    private function defineTranslationApiParameters(array $config, ContainerBuilder $container): void
    {
        $google = $config[FacadesRegistry::FACADE_GOOGLE] ?? [];
        $keys = [
            FacadesRegistry::FACADE_GOOGLE => $google['key'] ?? null,
        ];
        $container->setParameter('aeliot_trans_maintain.translation_api.keys', $keys);

        $limits = [
            FacadesRegistry::FACADE_GOOGLE => $google['limit'] ?? null,
        ];
        $container->setParameter('aeliot_trans_maintain.translation_api.limits', $limits);

        $version = (new KernelVersionDetector())->getVersion($container, 'symfony/translation');
        $projectDir = version_compare($version, '5.0.0', '>=') ? '%kernel.project_dir%' : '%kernel.root_dir%/..';
        $reportPath = sprintf('%s/var/aeliot_trans_maintain_limit_report.csv', $projectDir);

        $container->setParameter('aeliot_trans_maintain.translation_api.report_path', $reportPath);
    }
}
