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

final class KeysLinker
{
    /**
     * @param array<string,mixed> $array
     */
    public function glueKeys(array $array, string $prefix = null): \Generator
    {
        $prefix = null === $prefix ? '' : $prefix.'.';
        foreach ($array as $key => $value) {
            $key = $prefix.$key;
            if (\is_array($value)) {
                yield from $this->glueKeys($value, $key);
            } else {
                yield $key => $value;
            }
        }
    }
}
