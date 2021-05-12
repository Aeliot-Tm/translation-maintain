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

    public function getDomains(): array
    {
        $domains = array_keys($this->getFilesMap());
        sort($domains);

        return $domains;
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

    public function getLocales(): array
    {
        $mentionedLocales = array_unique(array_merge(...array_map('array_keys', array_values($this->getFilesMap()))));
        sort($mentionedLocales);

        return $mentionedLocales;
    }

    public function locateFile($domain, $locale): string
    {
        $pattern = \sprintf('~%s\b%s.%s.ya?ml$~', preg_quote(DIRECTORY_SEPARATOR, '~'), preg_quote($domain, '~'), preg_quote($locale, '~'));
        foreach ($this->getFiles() as $file) {
            /** @var \SplFileInfo $file */
            if (preg_match($pattern, $path = $file->getRealPath())) {
                return $path;
            }
        }

        return $this->directoryProvider->getDefault() . '/' . $domain . '.' . $locale . '.yaml';
    }

    /**
     * @return Finder|iterable<SplFileInfo>
     */
    private function getFiles(): Finder
    {
        return (new Finder())
            ->in($this->directoryProvider->getAll())
            ->name(['*.yaml', '*.yml'])
            ->files();
    }
}
