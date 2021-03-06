<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

use Aeliot\Bundle\TransMaintain\Service\Yaml\KeyValidationTrait;

final class KeysCleaner implements TransformerInterface
{
    use KeyValidationTrait;

    public function transform(array $yaml): array
    {
        foreach ($yaml as $rawKey => $value) {
            $key = $this->normaliseKey($rawKey);
            if ($key !== $rawKey) {
                unset($yaml[$rawKey]);
            }

            $yaml[$key] = \is_array($value) ? $this->transform($value) : $value;
        }

        return $yaml;
    }

    private function normaliseKey(string $rawKey): string
    {
        $key = $rawKey;
        if (preg_match('/^([\'"])(.+)([\'"])$/', $key, $matches)) {
            if ($matches[1] !== $matches[3]) {
                return $key;
            }

            $key = $matches[2];
        }

        return $this->isAcceptable($key) ? $key : $rawKey;
    }
}
