<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Test\Integration;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

abstract class IntegrationTestCase extends KernelTestCase
{
    protected static $booted = false;
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

        try {
            if (!static::$container) {
                $container = static::$kernel->getContainer();
                static::$container = $container->has('test.service_container')
                    ? $container->get('test.service_container')
                    : $container;
            }

            return static::$container;
        } catch (ServiceNotFoundException $e) {
            $message = 'Could not find service "test.service_container".'
                .' Try updating the "framework.test" config to "true".';
            throw new \LogicException($message, 0, $e);
        }
    }
}
