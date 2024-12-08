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

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

trait KeyValidationTrait
{
    private function isAcceptable(string $key): bool
    {
        return !preg_match('/[^a-zA-Z0-9._-]|\\.{2}|^\\.|\\.$/', $key);
    }

    private function isSplittable(string $key): bool
    {
        return $this->isAcceptable($key) && strpos($key, '.');
    }
}
