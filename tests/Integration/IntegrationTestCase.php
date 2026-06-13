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

namespace Aeliot\Bundle\TransMaintain\Test\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class IntegrationTestCase extends KernelTestCase
{
    /**
     * @var ContainerInterface|null
     */
    protected static $container;

    /**
     * Only used on Symfony 3.4 where KernelTestCase has no $booted property.
     *
     * @var bool
     */
    protected static $legacyKernelBooted = false;

    protected static function getContainer(): Container
    {
        if (method_exists(parent::class, 'getContainer')) {
            return parent::getContainer();
        }

        $isBooted = property_exists(parent::class, 'booted')
            ? static::$booted
            : static::$legacyKernelBooted;

        if (!$isBooted) {
            static::bootKernel();
            if (!property_exists(parent::class, 'booted')) {
                static::$legacyKernelBooted = true;
            }
        }

        if (!static::$container) {
            $container = static::$kernel->getContainer();
            static::$container = $container->has('test.service_container')
                ? $container->get('test.service_container')
                : $container;
        }

        return static::$container;
    }
}
