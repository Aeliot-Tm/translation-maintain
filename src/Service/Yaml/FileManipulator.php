<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Symfony\Component\Filesystem\Filesystem;

final class FileManipulator
{
    private Filesystem $filesystem;
    private YamlContentHandler $yamlContentHandler;

    public function __construct(Filesystem $filesystem, YamlContentHandler $yamlContentHandler)
    {
        $this->filesystem = $filesystem;
        $this->yamlContentHandler = $yamlContentHandler;
    }

    /**
     * @param array<string,mixed> $yaml
     */
    public function dump(string $pathOut, array $yaml): void
    {
        $this->filesystem->mkdir(\dirname($pathOut));

        $content = $this->yamlContentHandler->dump($yaml);
        $this->filesystem->dumpFile($pathOut, $content);
    }

    public function exists(string $path): bool
    {
        return $this->filesystem->exists($path);
    }

    /**
     * @return array<string,mixed>
     */
    public function parse(string $pathIn): array
    {
        if (!$this->filesystem->exists($pathIn)) {
            throw new \InvalidArgumentException(sprintf('Invalid path passed: "%s"', $pathIn));
        }

        return $this->yamlContentHandler->parseFile($pathIn) ?? [];
    }
}
