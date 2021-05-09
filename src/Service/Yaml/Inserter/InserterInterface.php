<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Inserter;

interface InserterInterface
{
    public function getPosition(): string;

    public function insert(array $yaml, string $id, string $value): array;
}
