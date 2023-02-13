<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Inserter;

interface InserterInterface
{
    public function getPosition(): string;

    /**
     * @param array<string,mixed> $yaml
     *
     * @return array<string,mixed>
     */
    public function insert(array $yaml, string $id, string $value): array;
}
