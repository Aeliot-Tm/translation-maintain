<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Model\KeysMissedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesMapProvider;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;
use Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry;

final class KeysMissedLinter implements LinterInterface
{
    private FilesMapProvider $filesMapProvider;
    private KeysParser $keysParser;

    public function __construct(FilesMapProvider $filesMapProvider, KeysParser $keysParser)
    {
        $this->filesMapProvider = $filesMapProvider;
        $this->keysParser = $keysParser;
    }

    public function getKey(): string
    {
        return 'keys_missed';
    }

    public function getPresets(): array
    {
        return [LinterRegistry::PRESET_BASE];
    }

    public function lint(): ReportBag
    {
        $bag = new ReportBag(KeysMissedLine::class);
        $domainsFiles = $this->filesMapProvider->getFilesMap();
        foreach ($domainsFiles as $domain => $localesFiles) {
            if (count($localesFiles) === 1) {
                continue;
            }
            $parsedKeys = $this->keysParser->getParsedKeys($localesFiles);
            $omittedKeys = $this->keysParser->getOmittedKeys($parsedKeys);
            $allOmittedKeys = $this->keysParser->mergeKeys($omittedKeys);

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
}
