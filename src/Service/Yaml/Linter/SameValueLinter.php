<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;

final class SameValueLinter implements LinterInterface
{
    use EmptyPresetsTrait;
    use GlueKeysTrait;

    private FileManipulator $fileManipulator;
    private FileMapFilter $fileMapFilter;

    public function __construct(FileManipulator $fileManipulator, FileMapFilter $fileMapFilter)
    {
        $this->fileManipulator = $fileManipulator;
        $this->fileMapFilter = $fileMapFilter;
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
                $pairs = array_merge(
                    ...array_map(
                        fn(string $x): array => iterator_to_array($this->glueKeys($this->fileManipulator->parse($x))),
                        $files
                    )
                );
                foreach ($pairs as $translationId => $translation) {
                    if (!\array_key_exists($translation, $values)) {
                        $values[$translation] = $translationId;
                    } else {
                        if (!\array_key_exists($translation, $same)) {
                            $same[$translation] = [$values[$translation]];
                        }

                        $same[$translation][] = $translationId;
                    }
                }

                ksort($same);

                foreach ($same as $translation => $translationIds) {
                    sort($translationIds);
                    $bag->addLine($domain, $locale, $translation, $translationIds);
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
                'locale' => ['string'],
                'translation' => ['string'],
                'translation_ids' => ['array'],
            ],
            'There is no keys with same value',
            'Translation keys with same values'
        );
    }
}
