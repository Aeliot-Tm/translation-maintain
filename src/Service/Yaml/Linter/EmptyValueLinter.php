<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;

final class EmptyValueLinter implements LinterInterface
{
    use EmptyPresetsTrait;

    private FileMapFilter $fileMapFilter;
    private FileToSingleLevelArrayParser $fileParser;

    public function __construct(FileMapFilter $fileMapFilter, FileToSingleLevelArrayParser $fileParser)
    {
        $this->fileMapFilter = $fileMapFilter;
        $this->fileParser = $fileParser;
    }

    public function getKey(): string
    {
        return 'empty_value';
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = $this->createReportBag();
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            $translationIsWithLocales = $this->getTranslationIsWithLocalesForEmptyValues($localesFiles);
            $this->addLines($bag, $domain, $translationIsWithLocales);
        }

        return $bag;
    }

    /**
     * @param array<string,array<string>> $translationIsWithLocales
     */
    private function addLines(ReportBag $bag, string $domain, array $translationIsWithLocales): void
    {
        ksort($translationIsWithLocales);

        foreach ($translationIsWithLocales as $translationId => $locales) {
            sort($locales);
            $bag->addLine($domain, $translationId, $locales);
        }
    }

    private function createReportBag(): ReportBag
    {
        return new ReportBag(
            [
                'domain' => ['string'],
                'translation_id' => ['string'],
                'locales' => ['array'],
            ],
            'There is no key with empty value',
            'Translation keys with empty values'
        );
    }

    /**
     * @param array<string,array<string>> $localesFiles
     *
     * @return array<string,array<string>>
     */
    private function getTranslationIsWithLocalesForEmptyValues(array $localesFiles): array
    {
        $translationIsWithLocales = [];
        foreach ($localesFiles as $locale => $files) {
            foreach ($files as $file) {
                foreach ($this->fileParser->parse($file) as $translationId => $value) {
                    if ('' === trim($value)) {
                        if (!\array_key_exists($translationId, $translationIsWithLocales)) {
                            $translationIsWithLocales[$translationId] = [];
                        }
                        $translationIsWithLocales[$translationId][] = $locale;
                    }
                }
            }
        }

        return $translationIsWithLocales;
    }
}
