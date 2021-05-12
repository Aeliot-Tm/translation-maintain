<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

use Aeliot\Bundle\TransMaintain\Service\DirectoryProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FilesFinder
{
    private DirectoryProvider $directoryProvider;

    public function __construct(DirectoryProvider $directoryProvider)
    {
        $this->directoryProvider = $directoryProvider;
    }

    /**
     * @return array<array>
     */
    public function getFilesMap(): array
    {
        $map = [];
        foreach ($this->getFiles() as $file) {
            $parts = explode('.', $file->getFilenameWithoutExtension());
            $locale = array_pop($parts);
            $domain = implode('.', $parts);
            if (!isset($map[$domain])) {
                $map[$domain] = [];
            }
            if (!isset($map[$domain][$locale])) {
                $map[$domain][$locale] = [];
            }
            $map[$domain][$locale][] = $file->getRealPath();
        }

        return $map;
    }

    /**
     * @return iterable<SplFileInfo>
     */
    private function getFiles(): iterable
    {
        return (new Finder())
            ->in($this->directoryProvider->getAll())
            ->name(['*.yaml', '*.yml'])
            ->files();
    }
}
