<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

trait KeyParserTrait
{
    /**
     * @param string[] $path
     * @param string|array<string,mixed> $value
     *
     * @return array<string,string|array<string,mixed>>
     */
    private function createNestedValue(array $path, $value): array
    {
        $step = array_shift($path);

        return [$step => $path ? $this->createNestedValue($path, $value) : $value];
    }
}
