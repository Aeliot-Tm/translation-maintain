<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain;

use Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass\DirectoryProviderCompilerPass;
use Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass\TranslatorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AeliotTransMaintainBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new DirectoryProviderCompilerPass());
        $container->addCompilerPass(new TranslatorCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -33);
    }
}
