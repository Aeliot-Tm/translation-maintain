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

use DG\BypassFinals;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__.'/../vendor/autoload.php';

BypassFinals::enable();

if (!class_exists(Dotenv::class)) {
    throw new LogicException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
}

// load all the .env files
$paths = ['.env.local', '.env'];
$paths = array_map(static fn (string $x): string => dirname(__DIR__).\DIRECTORY_SEPARATOR.$x, $paths);
$paths = array_filter($paths, static fn (string $x): bool => file_exists($x) && is_file($x));
if (method_exists(Dotenv::class, 'bootEnv')) {
    $dotenv = new Dotenv('APP_ENV', 'APP_DEBUG');
    array_walk($paths, static fn (string $x) => $dotenv->bootEnv($x, 'test'));
} elseif (method_exists(Dotenv::class, 'loadEnv')) {
    $dotenv = new Dotenv(false);
    array_walk($paths, static fn (string $x) => $dotenv->loadEnv($x, 'APP_ENV', 'test'));
} else {
    $dotenv = new Dotenv(false);
    array_walk($paths, static fn (string $x) => $dotenv->load($x));
}

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'test';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) ($_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], \FILTER_VALIDATE_BOOLEAN) ? '1' : '0');
