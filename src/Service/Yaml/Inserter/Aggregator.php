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

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Inserter;

use Aeliot\Bundle\TransMaintain\Exception\KeyCollisionException;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;

final class Aggregator implements InserterInterface
{
    public function getPosition(): string
    {
        return KeyRegister::TO_THE_END;
    }

    public function insert(array $yaml, string $id, string $value): array
    {
        if (isset($yaml[$id]) && $yaml[$id] !== $value) {
            throw new KeyCollisionException(sprintf('Key "%s" exists', $id));
        }
        $yaml[$id] = $value;

        return $yaml;
    }
}
