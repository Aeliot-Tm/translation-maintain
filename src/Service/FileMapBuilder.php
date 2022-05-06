<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service;

use Symfony\Component\Finder\SplFileInfo;

final class FileMapBuilder
{
    /**
     * @param iterable<SplFileInfo> $files
     *
     * @return array<string,array<string,array<int,string>>>
     */
    public function buildFilesMap(iterable $files): array
    {
        $map = [];
        foreach ($files as $file) {
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
}
