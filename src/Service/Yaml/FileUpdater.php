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

final class FileUpdater
{
    private FileManipulator $fileManipulator;
    private TransformationConveyor $transformationConveyor;

    public function __construct(FileManipulator $fileManipulator, TransformationConveyor $transformationConveyor)
    {
        $this->fileManipulator = $fileManipulator;
        $this->transformationConveyor = $transformationConveyor;
    }

    public function update(string $pathIn, string $pathOut): void
    {
        $yaml = $this->fileManipulator->parse($pathIn);
        $yaml = $this->transformationConveyor->transform($yaml);
        $this->fileManipulator->dump($pathOut, $yaml);
    }
}
