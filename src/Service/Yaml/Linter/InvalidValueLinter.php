<?php

declare(strict_types=1);

namespace Aeliot\Bundle\TransMaintain\Service\Yaml\Linter;

use Aeliot\Bundle\TransMaintain\Dto\LintYamlFilterDto;
use Aeliot\Bundle\TransMaintain\Model\InvalidValueLine;
use Aeliot\Bundle\TransMaintain\Model\ReportBag;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileMapFilter;
use Aeliot\Bundle\TransMaintain\Service\Yaml\FileToSingleLevelArrayParser;

final class InvalidValueLinter implements LinterInterface
{
    use EmptyPresetsTrait;

    private FileMapFilter $fileMapFilter;
    private FileToSingleLevelArrayParser $fileParser;
    private ?string $invalidValuePattern;

    public function __construct(FileToSingleLevelArrayParser $fileParser, FileMapFilter $fileMapFilter, ?string $invalidValuePattern)
    {
        $this->fileMapFilter = $fileMapFilter;
        $this->fileParser = $fileParser;
        $this->invalidValuePattern = $invalidValuePattern;
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

        $bag = new ReportBag(InvalidValueLine::class);
        $domainsFiles = $this->fileMapFilter->getFilesMap($filterDto);

        foreach ($domainsFiles as $domain => $localesFiles) {
            foreach ($localesFiles as $locale => $files) {
                $invalidValueKeys = [];

                foreach ($files as $file) {
                    foreach ($this->fileParser->parse($file) as $translationId => $value) {
                        if (preg_match($this->invalidValuePattern, $value)) {
                            $invalidValueKeys[] = $translationId;
                        }
                    }
                }

                sort($invalidValueKeys);

                foreach ($invalidValueKeys as $translationId) {
                    $bag->addLine(new InvalidValueLine($domain, $locale, $translationId));
                }
            }
        }

        return $bag;
    }
}
