<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml;

final class MissedValuesFinder
{
    private FilesFinder $filesFinder;
    private FileToSingleLevelArrayParser $fileParser;
    private KeysParser $keysParser;

    public function __construct(FilesFinder $filesFinder, FileToSingleLevelArrayParser $fileParser, KeysParser $keysParser)
    {
        $this->filesFinder = $filesFinder;
        $this->fileParser = $fileParser;
        $this->keysParser = $keysParser;
    }

    /**
     * @return array<string,string>
     */
    public function findMissedTranslations(string $domain, string $sourceLocale, ?string $targetLocale): array
    {
        $domainsFiles = $this->filesFinder->getFilesMap();

        if (!isset($domainsFiles[$domain][$sourceLocale])) {
            throw new \InvalidArgumentException(sprintf('Invalid domain "%s" or locale "%s" posted', $domain, $sourceLocale));
        }

        $parsedKeys = $this->keysParser->getParsedKeys($domainsFiles[$domain]);
        $omittedKeys = $this->keysParser->getOmittedKeys($parsedKeys);
        $allOmittedKeys = $this->keysParser->mergeKeys($omittedKeys);

        $values = array_intersect_key(
            $this->fileParser->parseFiles($domainsFiles[$domain][$sourceLocale]),
            array_flip($allOmittedKeys)
        );

        if ($targetLocale) {
            if (!$filterKeys = $omittedKeys[$targetLocale] ?? null) {
                throw new \InvalidArgumentException(sprintf('There is no omitted keys for locale "%s"', $targetLocale));
            }

            $values = array_intersect_key($values, array_flip($filterKeys));
        }

        return $values;
    }
}
