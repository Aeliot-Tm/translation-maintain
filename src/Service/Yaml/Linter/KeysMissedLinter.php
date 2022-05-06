<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
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
        $bag = $this->createReportBag();
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
                $omittedLocales = $this->getOmittedLocales($omittedKeys, (string) $translationId, $filterDto->locales);

                if ($omittedLocales) {
                    $bag->addLine($domain, $translationId, $omittedLocales);
                }
            }
        }

        return $bag;
    }

    private function createReportBag(): ReportBag
    {
        return new ReportBag(
            [
                'domain' => ['string'],
                'translation_id' => ['string'],
                'omitted_locales' => ['array'],
            ],
            'All locales of all domains are in the sync state. There are no missed translation keys',
            'Missed translation keys'
        );
    }

    /**
     * @param array<string,array<string>> $omittedKeys
     * @param string[]|null $filteredLocales
     *
     * @return string[]
     */
    private function getOmittedLocales(array $omittedKeys, string $translationId, ?array $filteredLocales): array
    {
        $omittedLocales = [];
        foreach ($omittedKeys as $locale => $keys) {
            if (\in_array($translationId, $keys, true)) {
                $omittedLocales[] = $locale;
            }
        }

        if ($filteredLocales) {
            $omittedLocales = array_intersect($omittedLocales, $filteredLocales);
        }

        return $omittedLocales;
    }
}
