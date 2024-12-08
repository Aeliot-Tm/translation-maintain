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

use PhpCsFixer\Finder;

return (new Finder())
    ->files()
    ->ignoreVCS(true)
    ->in(dirname(__DIR__, 2))
    ->exclude(['report', 'var', 'vendor']);
