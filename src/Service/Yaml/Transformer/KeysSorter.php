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

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

final class KeysSorter implements TransformerInterface
{
    public function transform(array $yaml): array
    {
        ksort($yaml);
        foreach ($yaml as $key => $value) {
            if (\is_array($value)) {
                $yaml[$key] = $this->transform($value);
            }
        }

        return $yaml;
    }
}
