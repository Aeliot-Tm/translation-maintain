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

use Aeliot\Bundle\TransMaintain\Service\DirectoryProvider;
use Aeliot\Bundle\TransMaintain\Service\FileMapBuilder;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

final class FilesFinder
{
    private DirectoryProvider $directoryProvider;
    private FileMapBuilder $fileMapBuilder;

    public function __construct(DirectoryProvider $directoryProvider, FileMapBuilder $fileMapBuilder)
    {
        $this->directoryProvider = $directoryProvider;
        $this->fileMapBuilder = $fileMapBuilder;
    }

    /**
     * @return array<int,string>
     */
    public function getDomains(): array
    {
        return array_keys($this->getFilesMap());
    }

    /**
     * @return array<string,array<string,array<int,string>>>
     */
    public function getFilesMap(): array
    {
        return $this->fileMapBuilder->buildFilesMap($this->getFiles());
    }

    /**
     * @deprecated since version 2.7.0. Use {@see \Aeliot\Bundle\TransMaintain\Service\LocalesDetector::getLocales() }
     *
     * @return array<int,string>
     */
    public function getLocales(): array
    {
        $mentionedLocales = array_unique(array_merge(...array_map('array_keys', array_values($this->getFilesMap()))));
        sort($mentionedLocales);

        return $mentionedLocales;
    }

    public function locateFile(string $domain, string $locale): string
    {
        $pattern = sprintf('~%s\b%s.%s.ya?ml$~', preg_quote(\DIRECTORY_SEPARATOR, '~'), preg_quote($domain, '~'), preg_quote($locale, '~'));
        foreach ($this->getFiles() as $file) {
            /** @var \SplFileInfo $file */
            if (preg_match($pattern, $path = $file->getRealPath())) {
                return $path;
            }
        }

        return $this->directoryProvider->getDefault().'/'.$domain.'.'.$locale.'.yaml';
    }

    /**
     * @return Finder<SplFileInfo>
     */
    private function getFiles(): Finder
    {
        return (new Finder())
            ->in($this->directoryProvider->getAll())
            ->name(['*.yaml', '*.yml'])
            ->files();
    }
}
