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

use Aeliot\Bundle\TransMaintain\Service\Yaml\Transformer\TransformerInterface;

final class TransformationConveyor
{
    /**
     * @var TransformerInterface[]
     */
    private iterable $transformers;

    /**
     * @param iterable<TransformerInterface> $transformers
     */
    public function __construct(iterable $transformers)
    {
        $this->transformers = $transformers;
    }

    /**
     * @param array<string,mixed> $yaml
     *
     * @return array<string,mixed>
     */
    public function transform(array $yaml): array
    {
        foreach ($this->transformers as $transformer) {
            $yaml = $transformer->transform($yaml);
        }

        return $yaml;
    }
}
