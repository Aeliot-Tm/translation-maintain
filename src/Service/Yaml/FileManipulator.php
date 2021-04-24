<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

final class FileManipulator
{
    private Filesystem $filesystem;
    private int $yamlIndent;

    public function __construct(Filesystem $filesystem, int $yamlIndent)
    {
        $this->filesystem = $filesystem;
        $this->yamlIndent = $yamlIndent;
    }

    public function dump(string $pathOut, array $yaml): void
    {
        $this->filesystem->mkdir(basename($pathOut));

        $dumpFlags = Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE | Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK;
        $content = Yaml::dump($yaml, 100, $this->yamlIndent, $dumpFlags);
        $this->filesystem->dumpFile($pathOut, $content);
    }

    public function parse(string $pathIn): array
    {
        if (!$this->filesystem->exists($pathIn)) {
            throw new \InvalidArgumentException(\sprintf('Invalid path passed: "%s"', $pathIn));
        }

        return Yaml::parseFile($pathIn, Yaml::PARSE_EXCEPTION_ON_INVALID_TYPE);
    }
}
