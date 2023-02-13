<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class IntegrationTestCase extends KernelTestCase
{
    /**
     * @var bool
     */
    protected static $booted = false;
    /**
     * @var ContainerInterface|null
     */
    protected static $container;

    protected static function getContainer(): ContainerInterface
    {
        if (method_exists(parent::class, 'getContainer')) {
            return parent::getContainer();
        }

        if (!static::$booted) {
            static::bootKernel();
            static::$booted = true;
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
