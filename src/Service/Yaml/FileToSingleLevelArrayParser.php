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

    /**
     * @return array<string,string>
     */
    public function parse(string $path): array
    {
        return iterator_to_array($this->keysLinker->glueKeys($this->fileManipulator->parse($path)));
    }

    /**
     * @param string[] $files
     *
     * @return array<string,string>
     */
    public function parseFiles(array $files): array
    {
        $yaml = array_merge(...array_map(fn (string $x): array => $this->parse($x), array_values($files)));
        ksort($yaml);

        return $yaml;
    }
}
