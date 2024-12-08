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
            $keys[$locale] = array_keys($this->fileParser->parseFiles($files));
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
     * @deprecated since version 2.7.0. Use {@see \Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser::parseFiles() }
     *
     * @param string[] $files
     *
     * @return array<string,string>
     */
    public function parseFiles(array $files): array
    {
        return $this->fileParser->parseFiles($files);
    }
}
