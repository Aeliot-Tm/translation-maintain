<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

interface TransformerInterface
{
    public function transform(array $yaml): array;
}
