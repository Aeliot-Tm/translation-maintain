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
        return $this->sort($this->buildMap($files));
    }

    /**
     * @param iterable<SplFileInfo> $files
     *
     * @return array<string,array<string,array<int,string>>>
     */
    private function buildMap(iterable $files): array
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

    /**
     * @param array<string,mixed> $map
     *
     * @return array<string,mixed>
     */
    private function sort(array $map): array
    {
        ksort($map);
        foreach ($map as $domain => $localesFiles) {
            ksort($localesFiles);
            $map[$domain] = $localesFiles;
        }

        return $map;
    }
}
