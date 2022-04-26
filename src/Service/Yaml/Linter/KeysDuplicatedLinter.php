<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileManipulator;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;

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
        return [LinterInterface::PRESET_BASE];
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        $bag = $this->createReportBag();
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            foreach ($localesFiles as $locale => $files) {
                $values = [];
                $duplicatedKeys = [];
                foreach ($files as $file) {
                    foreach ($this->glueKeys($this->fileManipulator->parse($file)) as $translationId => $value) {
                        if (\array_key_exists($translationId, $values)) {
                            $duplicatedKeys[] = $translationId;
                        } else {
                            $values[$translationId] = $value;
                        }
                    }
                }

                $duplicatedKeys = array_unique($duplicatedKeys);
                sort($duplicatedKeys);

                foreach ($duplicatedKeys as $translationId) {
                    $bag->addLine($domain, $locale, $translationId);
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
                'duplicated_translation_id' => ['string'],
            ],
            'There are no duplicated keys',
            'Duplicated translation keys'
        );
    }
}
