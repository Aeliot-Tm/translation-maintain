<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\KeysMissedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FilesFinder;
use Aeliot\Bundle\TransMaintain\Service\Yaml\KeysParser;

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
            if (1 === \count($localesFiles)) {
                continue;
            }

            $parsedKeys = $this->keysParser->getParsedKeys($localesFiles);
            $omittedKeys = $this->keysParser->getOmittedKeys($parsedKeys);
            $allOmittedKeys = $this->keysParser->mergeKeys($omittedKeys);

            foreach ($allOmittedKeys as $translationId) {
                $omittedLocales = [];
                foreach ($omittedKeys as $locale => $keys) {
                    if (\in_array($translationId, $keys, true)) {
                        $omittedLocales[] = $locale;
                    }
                }

                if ($filterDto->locales) {
                    $omittedLocales = array_intersect($omittedLocales, $filterDto->locales);
                }

                if ($omittedLocales) {
                    sort($omittedLocales);
                    $bag->addLine(new KeysMissedLine($domain, $translationId, $omittedLocales));
                }
            }
        }

        return $bag;
    }
}
