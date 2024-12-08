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

interface TransformerInterface
{
    /**
     * @param array<string,mixed> $yaml
     *
     * @return array<string,mixed>
     */
    public function transform(array $yaml): array;
}
