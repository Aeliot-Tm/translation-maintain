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

use Aeliot\Bundle\TransMaintain\Service\Yaml\BranchInjector;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyRegister;

final class Merger implements InserterInterface
{
    private Aggregator $aggregator;
    private BranchInjector $branchInjector;

    public function __construct(BranchInjector $branchInjector, Aggregator $aggregator)
    {
        $this->aggregator = $aggregator;
        $this->branchInjector = $branchInjector;
    }

    public function getPosition(): string
    {
        return KeyRegister::MERGE;
    }

    public function insert(array $yaml, string $id, string $value): array
    {
        if (!$this->branchInjector->inject($yaml, $id, $value)) {
            // fallback to the insertion of key-value "as is". use last chance of insertion
            $yaml = $this->aggregator->insert($yaml, $id, $value);
        }

        return $yaml;
    }
}
