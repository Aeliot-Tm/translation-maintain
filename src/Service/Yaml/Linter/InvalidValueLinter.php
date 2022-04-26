<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;

final class InvalidValueLinter implements LinterInterface
{
    use EmptyPresetsTrait;

    private FileMapFilter $fileMapFilter;
    private FileToSingleLevelArrayParser $fileParser;
    private ?string $invalidValuePattern;

    public function __construct(
        FileToSingleLevelArrayParser $fileParser,
        FileMapFilter $fileMapFilter,
        ?string $valueInvalidPattern
    ) {
        $this->fileMapFilter = $fileMapFilter;
        $this->fileParser = $fileParser;
        $this->invalidValuePattern = $valueInvalidPattern;
    }

    public function getKey(): string
    {
        return 'invalid_value';
    }

    public function lint(LintYamlFilterDto $filterDto): ReportBag
    {
        if (!$this->invalidValuePattern) {
            throw new \LogicException('Value forbidden pattern is not configured yet');
        }

        $bag = $this->createReportBag();
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            foreach ($localesFiles as $locale => $files) {
                $invalidValueKeys = $this->getInvalidValueKeys($files);
                $this->addLInes($bag, $domain, $locale, $invalidValueKeys);
            }
        }

        return $bag;
    }

    /**
     * @param string[] $invalidValueKeys
     */
    private function addLInes(ReportBag $bag, string $domain, string $locale, array $invalidValueKeys): void
    {
        sort($invalidValueKeys);

        foreach ($invalidValueKeys as $translationId) {
            $bag->addLine($domain, $locale, $translationId);
        }
    }

    private function createReportBag(): ReportBag
    {
        return new ReportBag(
            [
                'domain' => ['string'],
                'locale' => ['string'],
                'translation_id' => ['string'],
            ],
            'There is no value which is match forbidden pattern',
            'Translation values which is match forbidden pattern'
        );
    }

    /**
     * @param string[] $files
     *
     * @return string[]
     */
    private function getInvalidValueKeys(array $files): array
    {
        $invalidValueKeys = [];

        foreach ($files as $file) {
            foreach ($this->fileParser->parse($file) as $translationId => $value) {
                if (preg_match($this->invalidValuePattern, $value)) {
                    $invalidValueKeys[] = $translationId;
                }
            }
        }

        return $invalidValueKeys;
    }
}
