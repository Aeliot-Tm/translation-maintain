<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\Yaml\Linter\GlueKeysTrait;

final class FileToSingleLevelArrayParser
{
    use GlueKeysTrait;

    private FileManipulator $fileManipulator;

    public function __construct(FileManipulator $fileManipulator)
    {
        $this->fileManipulator = $fileManipulator;
    }

    public function parse(string $path): array
    {
        return iterator_to_array($this->glueKeys($this->fileManipulator->parse($path)));
    }
}
