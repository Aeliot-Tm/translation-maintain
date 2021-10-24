<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\EmptyValueLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;

final class EmptyValueLinter implements LinterInterface
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
        return 'empty_value';
    }

    public function getPresets(): array
    {
        return [];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = new ReportBag(EmptyValueLine::class);
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            $empty = [];
            foreach ($localesFiles as $locale => $files) {
                foreach ($files as $file) {
                    foreach ($this->glueKeys($this->fileManipulator->parse($file)) as $languageId => $value) {
                        if (trim($value) === '') {
                            if (!array_key_exists($languageId, $empty)) {
                                $empty[$languageId] = [];
                            }
                            $empty[$languageId][] = $locale;
                        }
                    }
                }
            }

            ksort($empty);

            foreach ($empty as $languageId => $locales) {
                sort($locales);
                $bag->addLine(new EmptyValueLine($domain, $languageId, $locales));
            }
        }

        return $bag;
    }
}
