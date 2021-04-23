<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

trait KeyValidationTrait
{
    private function isAcceptable(string $key): bool
    {
        return !preg_match('/[^a-zA-Z0-9._-]|\\.{2}|^\\.|\\.$/', $key);
    }

    private function isSplittable(string $key): bool
    {
        return $this->isAcceptable($key) && strpos($key, '.');
    }
}
