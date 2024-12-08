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
            $translationIdsWithLocales = $this->getTranslationIdsWithLocalesForEmptyValues($localesFiles);
            $this->addLines($bag, $domain, $translationIdsWithLocales);
        }

        return $bag;
    }

    /**
     * @param array<string,array<string>> $translationIdsWithLocales
     */
    private function addLines(ReportBag $bag, string $domain, array $translationIdsWithLocales): void
    {
        foreach ($translationIdsWithLocales as $translationId => $locales) {
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
    private function getTranslationIdsWithLocalesForEmptyValues(array $localesFiles): array
    {
        $translationIdsWithLocales = [];
        foreach ($localesFiles as $locale => $files) {
            foreach ($this->fileParser->parseFiles($files) as $translationId => $value) {
                if ('' === trim($value)) {
                    if (!\array_key_exists($translationId, $translationIdsWithLocales)) {
                        $translationIdsWithLocales[$translationId] = [];
                    }
                    $translationIdsWithLocales[$translationId][] = $locale;
                }
            }
        }

        ksort($translationIdsWithLocales);

        return $translationIdsWithLocales;
    }
}
