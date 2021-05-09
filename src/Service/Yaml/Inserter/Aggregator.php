<?php

declare(strict_types=1);

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
            throw new KeyCollisionException(\sprintf('Key "%s" exists', $id));
        }
        $yaml[$id] = $value;

        return $yaml;
    }
}
