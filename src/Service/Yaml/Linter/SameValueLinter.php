<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Model\SameValueLine;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;

final class SameValueLinter implements LinterInterface
{
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

    public function getPresets(): array
    {
        return [];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = new ReportBag(SameValueLine::class);
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
                foreach ($pairs as $languageId => $translation) {
                    if (!array_key_exists($translation, $values)) {
                        $values[$translation] = $languageId;
                    } else {
                        if (!array_key_exists($translation, $same)) {
                            $same[$translation] = [$values[$translation]];
                        }

                        $same[$translation][] = $languageId;
                    }
                }

                ksort($same);

                foreach ($same as $translation => $languageIds) {
                    sort($languageIds);
                    $bag->addLine(new SameValueLine($domain, $locale, $translation, $languageIds));
                }
            }
        }

        return $bag;
    }
}
