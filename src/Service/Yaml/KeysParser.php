<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

final class KeysParser
{
    private FileToSingleLevelArrayParser $fileParser;

    public function __construct(FileToSingleLevelArrayParser $fileParser)
    {
        $this->fileParser = $fileParser;
    }

    /**
     * @param array<string,array<string>> $localesKeys
     *
     * @return array<string,array<string>>
     */
    public function getOmittedKeys(array $localesKeys): array
    {
        $allDomainKeys = $this->mergeKeys($localesKeys);
        $omittedKeys = [];
        foreach ($localesKeys as $locale => $localeKeys) {
            $omittedKeys[$locale] = array_values(array_diff($allDomainKeys, $localeKeys));
        }

        return $omittedKeys;
    }

    /**
     * @param array<string,array<string>> $localesFiles
     *
     * @return array<string,array<string>>
     */
    public function getParsedKeys(array $localesFiles): array
    {
        $keys = [];
        foreach ($localesFiles as $locale => $files) {
            $keys[$locale] = array_keys($this->parseFiles($files));
        }

        return $keys;
    }

    /**
     * @param array<string,array<string>> $localesKeys
     *
     * @return string[]
     */
    public function mergeKeys(array $localesKeys): array
    {
        $merged = array_unique(array_merge(...array_values($localesKeys)));
        sort($merged);

        return $merged;
    }

    /**
     * @param string[] $files
     *
     * @return array<string,string>
     */
    public function parseFiles(array $files): array
    {
        $yaml = array_merge(...array_map(fn (string $x): array => $this->fileParser->parse($x), array_values($files)));
        ksort($yaml);

        return $yaml;
    }
}
