<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

final class KeysSorter implements TransformerInterface
{
    public function transform(array $yaml): array
    {
        ksort($yaml);
        foreach ($yaml as $key => $value) {
            if (is_array($value)) {
                $yaml[$key] = $this->transform($value);
            }
        }

        return $yaml;
    }
}
