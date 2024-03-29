<?php

declare(strict_types=1);

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
