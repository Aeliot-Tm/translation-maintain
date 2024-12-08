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

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyValidationTrait;

final class KeysCleaner implements TransformerInterface
{
    use KeyValidationTrait;

    public function transform(array $yaml): array
    {
        foreach ($yaml as $rawKey => $value) {
            $key = $this->normaliseKey($rawKey);
            if ($key !== $rawKey) {
                unset($yaml[$rawKey]);
            }

            $yaml[$key] = \is_array($value) ? $this->transform($value) : $value;
        }

        return $yaml;
    }

    private function normaliseKey(string $rawKey): string
    {
        $key = $rawKey;
        if (preg_match('/^([\'"])(.+)([\'"])$/', $key, $matches)) {
            if ($matches[1] !== $matches[3]) {
                return $key;
            }

            $key = $matches[2];
        }

        return $this->isAcceptable($key) ? $key : $rawKey;
    }
}
