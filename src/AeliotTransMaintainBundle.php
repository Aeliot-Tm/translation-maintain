<?php

declare(strict_types=1);

/*
 * This file is part of the TransMaintain.
 *
 * (c) Anatoliy Melnikov <5785276@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

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
