<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

interface TransformerInterface
{
    /**
     * @param array<string,mixed> $yaml
     *
     * @return array<string,mixed>
     */
    public function transform(array $yaml): array;
}
