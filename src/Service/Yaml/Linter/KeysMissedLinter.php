<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\KeysMissedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;
use Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry;

final class KeysMissedLinter implements LinterInterface
{
    private FilesFinder $filesFinder;
    private KeysParser $keysParser;

    public function __construct(FilesFinder $filesFinder, KeysParser $keysParser)
    {
        $this->filesFinder = $filesFinder;
        $this->keysParser = $keysParser;
    }

    public function getKey(): string
    {
        return 'keys_missed';
    }

    public function getPresets(): array
    {
        return [LinterInterface::PRESET_BASE];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = new ReportBag(KeysMissedLine::class);
        $domainsFiles = $this->filesFinder->getFilesMap();
        foreach ($domainsFiles as $domain => $localesFiles) {
            if ($filterDto->domains && !\in_array($domain, $filterDto->domains, true)) {
                continue;
            }
            if (count($localesFiles) === 1) {
                continue;
            }

            $parsedKeys = $this->keysParser->getParsedKeys($localesFiles);
            $omittedKeys = $this->keysParser->getOmittedKeys($parsedKeys);
            $allOmittedKeys = $this->keysParser->mergeKeys($omittedKeys);

            foreach ($allOmittedKeys as $languageId) {
                $omittedLanguages = [];
                foreach ($omittedKeys as $locale => $keys) {
                    if (\in_array($languageId, $keys, true)) {
                        $omittedLanguages[] = $locale;
                    }
                }

                if ($filterDto->locales) {
                    $omittedLanguages = array_intersect($omittedLanguages, $filterDto->locales);
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
