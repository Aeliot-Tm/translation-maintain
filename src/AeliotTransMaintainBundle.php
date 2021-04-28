<?php

namespace Aeliot\Bundle\TransMaintain;

use Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass\KeyRegisterCompilerPass;
use Aeliot\Bundle\TransMaintain\DependencyInjection\CompilerPass\TranslatorCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class AeliotTransMaintainBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new KeyRegisterCompilerPass());
        $container->addCompilerPass(new TranslatorCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -33);
    }
}
