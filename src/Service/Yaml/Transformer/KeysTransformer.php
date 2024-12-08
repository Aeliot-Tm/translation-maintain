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
            /** @var string $key */
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
