<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\KeysDuplicatedLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\LinterRegistry;

final class KeysDuplicatedLinter implements LinterInterface
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
        return 'keys_duplicated';
    }

    public function getPresets(): array
    {
        return [LinterRegistry::PRESET_BASE];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = new ReportBag(KeysDuplicatedLine::class);
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            foreach ($localesFiles as $locale => $files) {
                $values = [];
                $duplicatedKeys = [];
                foreach ($files as $file) {
                    foreach ($this->glueKeys($this->fileManipulator->parse($file)) as $languageId => $value) {
                        if (array_key_exists($languageId, $values)) {
                            $duplicatedKeys[] = $languageId;
                        } else {
                            $values[$languageId] = $value;
                        }
                    }
                }

                $duplicatedKeys = array_unique($duplicatedKeys);
                sort($duplicatedKeys);

                foreach ($duplicatedKeys as $languageId) {
                    $bag->addLine(new KeysDuplicatedLine($domain, $locale, $languageId));
                }
            }
        }

        return $bag;
    }
}
