<?php

declare(strict_types=1);

use PhpCsFixer\Finder;

return (new Finder())
    ->files()
    ->ignoreVCS(true)
    ->in(dirname(__DIR__, 2))
    ->exclude(['report', 'var', 'vendor']);
