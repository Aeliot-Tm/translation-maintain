<?php

declare(strict_types=1);

use DG\BypassFinals;
use Symfony\Component\Dotenv\Dotenv;

require_once __DIR__.'/../vendor/autoload.php';
require_once __DIR__.'/Kernel.php';

if (!class_exists(Dotenv::class)) {
    throw new LogicException('Please run "composer require symfony/dotenv" to load the ".env" files configuring the application.');
}

// load all the .env files
$paths = ['.env.local', '.env'];
$paths = array_map(static fn (string $x): string => dirname(__DIR__).\DIRECTORY_SEPARATOR.$x, $paths);
$paths = array_filter($paths, static fn (string $x): bool => file_exists($x) && is_file($x));
$dotenv = new Dotenv('APP_ENV', 'APP_DEBUG');
array_walk($paths, static fn (string $x) => $dotenv->bootEnv($x, 'test'));

$_SERVER += $_ENV;
$_SERVER['APP_ENV'] = $_ENV['APP_ENV'] = ($_SERVER['APP_ENV'] ?? $_ENV['APP_ENV'] ?? null) ?: 'dev';
$_SERVER['APP_DEBUG'] = $_SERVER['APP_DEBUG'] ?? $_ENV['APP_DEBUG'] ?? 'prod' !== $_SERVER['APP_ENV'];
$_SERVER['APP_DEBUG'] = $_ENV['APP_DEBUG'] = (int) $_SERVER['APP_DEBUG'] || filter_var($_SERVER['APP_DEBUG'], \FILTER_VALIDATE_BOOLEAN) ? '1' : '0';

BypassFinals::enable();
