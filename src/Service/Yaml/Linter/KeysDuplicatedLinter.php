<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;

final class KeysDuplicatedLinter implements LinterInterface
{
    private FileMapFilter $fileMapFilter;
    private FileToSingleLevelArrayParser $fileParser;

    public function __construct(FileMapFilter $fileMapFilter, FileToSingleLevelArrayParser $fileParser)
    {
        $this->fileMapFilter = $fileMapFilter;
        $this->fileParser = $fileParser;
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
                $duplicatedKeys = $this->getDuplicatedKeys($files);
                $this->addLines($bag, $domain, $locale, $duplicatedKeys);
            }
        }

        return $bag;
    }

    /**
     * @param string[] $duplicatedKeys
     */
    private function addLines(ReportBag $bag, string $domain, string $locale, array $duplicatedKeys): void
    {
        $duplicatedKeys = array_unique($duplicatedKeys);
        sort($duplicatedKeys);

        foreach ($duplicatedKeys as $translationId) {
            $bag->addLine($domain, $locale, $translationId);
        }
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

    /**
     * @param string[] $files
     *
     * @return string[]
     */
    private function getDuplicatedKeys(array $files): array
    {
        $values = [];
        $duplicatedKeys = [];
        foreach ($files as $file) {
            foreach ($this->fileParser->parse($file) as $translationId => $value) {
                if (\array_key_exists($translationId, $values)) {
                    $duplicatedKeys[] = $translationId;
                } else {
                    $values[$translationId] = $value;
                }
            }
        }

        return $duplicatedKeys;
    }
}
