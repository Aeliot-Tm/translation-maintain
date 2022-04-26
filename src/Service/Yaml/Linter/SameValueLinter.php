<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;

final class SameValueLinter implements LinterInterface
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
        return 'same_value';
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = $this->createReportBag();
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            foreach ($localesFiles as $locale => $files) {
                $same = [];
                $values = [];
                foreach ($files as $file) {
                    foreach ($this->fileParser->parse($file) as $translationId => $translation) {
                        if (!\array_key_exists($translation, $values)) {
                            $values[$translation] = $translationId;
                        } else {
                            if (!\array_key_exists($translation, $same)) {
                                $same[$translation] = [$values[$translation]];
                            }

                            $same[$translation][] = $translationId;
                        }
                    }
                }

                $this->addLines($bag, $domain, $locale, $same);
            }
        }

        return $bag;
    }

    private function addLines(ReportBag $bag, $domain, $locale, array $same): void
    {
        ksort($same);

        foreach ($same as $translation => $translationIds) {
            sort($translationIds);
            $bag->addLine($domain, $locale, $translation, $translationIds);
        }
    }

    private function createReportBag(): ReportBag
    {
        return new ReportBag(
            [
                'domain' => ['string'],
                'locale' => ['string'],
                'translation' => ['string'],
                'translation_ids' => ['array'],
            ],
            'There is no keys with same value',
            'Translation keys with same values'
        );
    }
}
