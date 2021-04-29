<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Model\KeysMissedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesMapProvider;

final class KeysMissedLinter implements LinterInterface
{
    use GlueKeysTrait;

    private FileManipulator $fileManipulator;
    private FilesMapProvider $filesMapProvider;

    public function __construct(FileManipulator $fileManipulator, FilesMapProvider $filesMapProvider)
    {
        $this->fileManipulator = $fileManipulator;
        $this->filesMapProvider = $filesMapProvider;
    }

    public function getKey(): string
    {
        return 'keys_missed';
    }

    public function lint(): ReportBag
    {
        $bag = new ReportBag(KeysMissedLine::class);
        $domainsFiles = $this->filesMapProvider->getFilesMap();
        foreach ($domainsFiles as $domain => $localesFiles) {
            if (count($localesFiles) === 1) {
                continue;
            }
            $parsedKeys = $this->getParsedKeys($localesFiles);
            $omittedKeys = $this->getOmittedKeys($parsedKeys);
            $allOmittedKeys = $this->mergeKeys($omittedKeys);

            foreach ($allOmittedKeys as $languageId) {
                $omittedLanguages = [];
                foreach ($omittedKeys as $locale => $keys) {
                    if (in_array($languageId, $keys, true)) {
                        $omittedLanguages[] = $locale;
                    }
                }
                if ($omittedLanguages) {
                    sort($omittedLanguages);
                    $bag->addLine(new KeysMissedLine($domain, $languageId, $omittedLanguages));
                }
            }
        }

        return $bag;
    }

    private function getParsedKeys(array $localesFiles): array
    {
        $keys = [];
        foreach ($localesFiles as $locale => $files) {
            $keys[$locale] = $this->parseFiles($files);
        }

        return $keys;
    }

    private function mergeKeys(array $keys): array
    {
        $merged = array_unique(array_merge(...array_values($keys)));
        sort($merged);

        return $merged;
    }

    private function parseFiles(array $files): array
    {
        $values = [];
        foreach ($files as $file) {
            foreach ($this->glueKeys($this->fileManipulator->parse($file)) as $key => $value) {
                $values[$key] = $value;
            }
        }

        $keys = array_keys($values);
        sort($keys);

        return $keys;
    }

    private function getOmittedKeys(array $parsedKeys): array
    {
        $allDomainKeys = $this->mergeKeys($parsedKeys);
        $locales = array_keys($parsedKeys);
        sort($locales);
        $omittedKeys = array_fill_keys($locales, null);
        foreach ($parsedKeys as $locale => $localeKeys) {
            $omittedKeys[$locale] = array_diff($allDomainKeys, $localeKeys);
        }

        return $omittedKeys;
    }
}
