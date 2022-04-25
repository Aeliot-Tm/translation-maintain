<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer;

use Aeliot\Bundle\TransMaintain\Service\Yaml\BranchInjector;

final class KeysTransformer implements TransformerInterface
{
    private BranchInjector $injector;

    public function __construct(BranchInjector $injector)
    {
        $this->injector = $injector;
    }

    public function transform(array $yaml): array
    {
        foreach (array_keys($yaml) as $key) {
            if (\is_array($value = $yaml[$key])) {
                $value = $yaml[$key] = $this->transform($value);
            }
            unset($yaml[$key]);
            if (!$this->injector->inject($yaml, $key, $value)) {
                $yaml[$key] = $value;
            }
        }

        return $yaml;
    }
}
