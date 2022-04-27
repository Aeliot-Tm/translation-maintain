<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

final class FileToSingleLevelArrayParser
{
    private FileManipulator $fileManipulator;
    private KeysLinker $keysLinker;

    public function __construct(FileManipulator $fileManipulator, KeysLinker $keysLinker)
    {
        $this->fileManipulator = $fileManipulator;
        $this->keysLinker = $keysLinker;
    }

    public function parse(string $path): array
    {
        return iterator_to_array($this->keysLinker->glueKeys($this->fileManipulator->parse($path)));
    }
}
