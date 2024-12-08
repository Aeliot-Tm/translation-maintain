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
