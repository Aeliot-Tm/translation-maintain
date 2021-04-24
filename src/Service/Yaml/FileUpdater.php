<?php

declare(strict_types=1);

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
