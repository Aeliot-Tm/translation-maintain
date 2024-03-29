<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

final class KeysLinker
{
    /**
     * @param array<string,mixed> $array
     */
    public function glueKeys(array $array, string $prefix = null): \Generator
    {
        $prefix = null === $prefix ? '' : $prefix.'.';
        foreach ($array as $key => $value) {
            $key = $prefix.$key;
            if (\is_array($value)) {
                yield from $this->glueKeys($value, $key);
            } else {
                yield $key => $value;
            }
        }
    }
}
