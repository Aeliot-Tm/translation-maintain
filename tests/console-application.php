<?php

declare(strict_types=1);

use Aeliot\Bundle\TransMaintain\Test\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;

require __DIR__.'/bootstrap.php';

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

return new Application($kernel);
